<?php

require_once './pdo_conexion.php';  // Go up one directory since inventario_vacuno.php is in the vacuno folder
// Now you can use $conn for database queries

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ********** Indices de Produccion **********

// Initialize variables
$totalLactancyDays = 0;
$count = 0;

// Get today's date
$currentDate = date('Y-m-d');

// SQL query to calculate average lactancy days
$sql = "
    SELECT 
        n.tagid,
        n.fecha_nacimiento,
        COALESCE(d.vh_destete_fecha, '$currentDate') AS end_date
    FROM 
        vacuno n
    LEFT JOIN 
        vh_destete d ON n.tagid = d.vh_destete_tagid
    WHERE 
        n.etapa = 'inicio'
";

// Execute the query
$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Calculate the difference in days
        $lactancyDays = (strtotime($row['end_date']) - strtotime($row['fecha_nacimiento'])) / (60 * 60 * 24);
        
        // Only count if lactancyDays is non-negative
        if ($lactancyDays >= 0) {
            $totalLactancyDays += $lactancyDays; // Accumulate total lactancy days
            $count++; // Increment count of entries
        }
    }
}

// Calculate average if count is greater than 0 to avoid division by zero
$averageLactancyDays = $count > 0 ? $totalLactancyDays / $count : 0;

// Now you can use $averageLactancyDays as needed

// Indice Vacas en Ordeño

$totalInicio = 0;
$totalVacas = 0;
$vacasEnOrdenoPercentage = 0;

// SQL query to count tagids with etapa equal to 'Inicio'
$sql_inicio = "
    SELECT COUNT(DISTINCT tagid) AS total_inicio
    FROM vacuno
    WHERE grupo = 'Ternero' OR grupo = 'Ternera'
";

// Execute the query for total inicio
$result_inicio = mysqli_query($conn, $sql_inicio);
if ($result_inicio) {
    $row = mysqli_fetch_assoc($result_inicio);
    $totalInicio = $row['total_inicio'];
}

// SQL query to count tagids with grupo equal to 'Vaca'
$sql_vacas = "
    SELECT COUNT(DISTINCT tagid) AS total_vacas
    FROM vacuno
    WHERE grupo = 'Vaca'
";

// Execute the query for total vacas
$result_vacas = mysqli_query($conn, $sql_vacas);
if ($result_vacas) {
    $row = mysqli_fetch_assoc($result_vacas);
    $totalVacas = $row['total_vacas'];
}

// Calculate the percentage of vacas en ordeño
if ($totalVacas > 0) {
    $vacasEnOrdenoPercentage = ($totalInicio / $totalVacas) * 100;
} else {
    $vacasEnOrdenoPercentage = 0; // Avoid division by zero
}

// Now you can use $vacasEnOrdenoPercentage as needed

// Fetch average monthly Leche production
$avgLecheQuery = "
    SELECT 
        DATE_FORMAT(vh_leche_fecha, '%Y-%m') AS production_month, 
        AVG(vh_leche_peso) AS average_leche
    FROM 
        vh_leche
    GROUP BY 
        production_month
    ORDER BY 
        production_month ASC
";

$avgLecheResult = mysqli_query($conn, $avgLecheQuery);

$avgLecheLabels = [];
$avgLecheData = [];

if ($avgLecheResult && mysqli_num_rows($avgLecheResult) > 0) {
    while ($row = mysqli_fetch_assoc($avgLecheResult)) {
        $avgLecheLabels[] = $row['production_month'];
        $avgLecheData[] = round($row['average_leche'], 2);
    }
} else {
    // Handle case when there are no records
    $avgLecheLabels = ['No Data'];
    $avgLecheData = [0];
}

// Fetch monthly revenue from Leche production
$monthlyRevenueQuery = "
    SELECT 
        DATE_FORMAT(vh_leche_fecha, '%Y-%m') AS revenue_month, 
        SUM(vh_leche_peso * vh_leche_precio) AS total_revenue
    FROM 
        vh_leche
    GROUP BY 
        revenue_month
    ORDER BY 
        revenue_month ASC
";

$monthlyLecheRevenueResult = $conn->query($monthlyRevenueQuery);

$revenueLecheLabels = [];
$revenueLecheData = [];
$cumulativeRevenueLecheData = [];
$cumulativeTotalLeche = 0;

if ($monthlyLecheRevenueResult && $monthlyLecheRevenueResult->num_rows > 0) {
    while ($row = $monthlyLecheRevenueResult->fetch_assoc()) {
        $revenueLecheLabels[] = $row['revenue_month'];
        $revenueLecheData[] = round($row['total_revenue'], 2);
        
        // Calculate cumulative revenue
        $cumulativeTotalLeche += round($row['total_revenue'], 2);
        $cumulativeRevenueLecheData[] = $cumulativeTotalLeche;
    }
} else {
    $revenueLecheLabels = ['No Data'];
    $revenueLecheData = [0];
    $cumulativeRevenueLecheData = [0];
}

// Fetch monthly average weight data
$monthlyAverageWeightQuery = "
    SELECT 
        DATE_FORMAT(vh_peso_fecha, '%Y-%m') AS weight_month, 
        AVG(vh_peso_animal) AS average_weight
    FROM 
        vh_peso
    GROUP BY 
        weight_month
    ORDER BY 
        weight_month ASC
";

$monthlyAverageWeightResult = $conn->query($monthlyAverageWeightQuery);

$weightLabels = [];
$averageWeightData = [];

if ($monthlyAverageWeightResult && $monthlyAverageWeightResult->num_rows > 0) {
    while ($row = $monthlyAverageWeightResult->fetch_assoc()) {
        $weightLabels[] = $row['weight_month'];
        $averageWeightData[] = round($row['average_weight'], 2);
    }
} else {
    $weightLabels = ['No Data'];
    $averageWeightData = [0];
}

// Fetch monthly average revenue data
$monthlyAverageRevenueQuery = "
    SELECT 
        DATE_FORMAT(vh_peso_fecha, '%Y-%m') AS revenue_month, 
        AVG(vh_peso_animal * vh_peso_precio) AS average_revenue
    FROM 
        vh_peso
    GROUP BY 
        revenue_month
    ORDER BY 
        revenue_month ASC
";

$monthlyAverageRevenueResult = $conn->query($monthlyAverageRevenueQuery);

$revenuePesoLabels = [];
$averagePesoRevenueData = [];

if ($monthlyAverageRevenueResult && $monthlyAverageRevenueResult->num_rows > 0) {
    while ($row = $monthlyAverageRevenueResult->fetch_assoc()) {
        $revenuePesoLabels[] = $row['revenue_month'];
        $averagePesoRevenueData[] = round($row['average_revenue'], 2);
    }
} else {
    $revenuePesoLabels = ['No Data'];
    $averagePesoRevenueData = [0];
}

// Peso Promedio Mensual
$monthlyAverages = [];

// SQL query to calculate the average weight per month
$sql = "
    SELECT 
        DATE_FORMAT(vh_peso_fecha, '%Y-%m') AS peso_month, 
        AVG(vh_peso_animal) AS average_weight 
    FROM 
        vh_peso 
    GROUP BY 
        peso_month 
    ORDER BY 
        peso_month ASC
";

// Execute the query
$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $monthlyAverages[$row['peso_month']] = $row['average_weight'];
    }
}

// Calculate month-by-month average change
$averageChanges = [];
$previousMonth = null;
$previousAverage = null;

foreach ($monthlyAverages as $month => $average) {
    if ($previousMonth !== null) {
        // Calculate the change from the previous month
        $change = $average - $previousAverage;
        $averageChanges[$previousMonth . ' to ' . $month] = $change;
    }
    // Update previous values
    $previousMonth = $month;
    $previousAverage = $average;
}

// Initialize the average monthly weight variable
$averageMonthlyWeight = 0;

// Calculate the total change and count of entries
$totalChange = 0;
$countChanges = count($averageChanges);

foreach ($averageChanges as $change) {
    $totalChange += $change; // Accumulate total change
}

// Calculate the average if there are changes
if ($countChanges > 0) {
    $averageMonthlyWeight = $totalChange / $countChanges;
} else {
    $averageMonthlyWeight = 0; // Avoid division by zero
}

// Now you can use $averageMonthlyWeight as needed

// Initialize the total costo concentrado variable
$total_costo_concentrado = 0;

// Get today's date
$currentDate = date('Y-m-d');

// SQL query to calculate total costo concentrado
$sql = "
    SELECT 
        SUM(vh_concentrado_racion * vh_concentrado_costo * DATEDIFF('$currentDate', vh_concentrado_fecha)) AS total_costo
    FROM 
        vh_concentrado
";

// Execute the query
$result = mysqli_query($conn, $sql);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $total_costo_concentrado = $row['total_costo'] ?? 0; // Use null coalescing to avoid undefined index
}

// Now you can use $total_costo_concentrado as needed

// Initialize the total market value variable
$totalMarketValue = 0;

// SQL query to calculate the market value of all animals
$sql = "
    SELECT 
        p.vh_peso_tagid,
        p.vh_peso_animal,
        p.vh_peso_precio
    FROM 
        vh_peso p
    INNER JOIN (
        SELECT 
            vh_peso_tagid, 
            MAX(vh_peso_fecha) AS max_date
        FROM 
            vh_peso
        GROUP BY 
            vh_peso_tagid
    ) AS latest ON p.vh_peso_tagid = latest.vh_peso_tagid AND p.vh_peso_fecha = latest.max_date
";

// Execute the query
$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Calculate the market value for each animal
        $marketValue = $row['vh_peso_animal'] * $row['vh_peso_precio'];
        $totalMarketValue += $marketValue; // Accumulate total market value
    }
}

// Now you can use $totalMarketValue as needed
$concentradoImpact = $total_costo_concentrado/$totalMarketValue;

// ********** Indices de Alimentacion **********
// Calculos Graficos de Alimento Concentrado
$sql = "SELECT vh_concentrado_etapa, SUM(vh_concentrado_racion * vh_concentrado_costo) AS total
        FROM vh_concentrado
        GROUP BY vh_concentrado_etapa";

$result = mysqli_query($conn, $sql);

$inicio_percentaje = 0;
$crecimiento_percentaje = 0;
$finalizacion_percentaje = 0;
$total = 0; // Initialize total variable

if ($result) {
    // First pass to calculate total
    while ($row = mysqli_fetch_assoc($result)) {
        $total += $row['total']; // Accumulate total
    }
    // Reset result pointer to fetch data again
    mysqli_data_seek($result, 0);

    // Second pass to calculate percentages
    while ($row = mysqli_fetch_assoc($result)) {
        switch ($row['vh_concentrado_etapa']) {
            case 'Inicio':
                $inicio_percentaje = ($row['total'] / $total) * 100;
                break;
            case 'Crecimiento':
                $crecimiento_percentaje = ($row['total'] / $total) * 100;
                break;
            case 'Finalizacion':
                $finalizacion_percentaje = ($row['total'] / $total) * 100;
                break;
        }
    }
}

// Calculos Graficos de Sal
$sql = "SELECT vh_sal_etapa, SUM(vh_sal_racion * vh_sal_costo) AS total
        FROM vh_sal
        GROUP BY vh_sal_etapa";

$result = mysqli_query($conn, $sql);

$inicio_sal_percentaje = 0;
$crecimiento_sal_percentaje = 0;
$finalizacion_sal_percentaje = 0;
$total = 0; // Initialize total variable

if ($result) {
    // First pass to calculate total
    while ($row = mysqli_fetch_assoc($result)) {
        $total += $row['total']; // Accumulate total
    }
    // Reset result pointer to fetch data again
    mysqli_data_seek($result, 0);

    // Second pass to calculate percentages
    while ($row = mysqli_fetch_assoc($result)) {
        switch ($row['vh_sal_etapa']) {
            case 'Inicio':
                $inicio_sal_percentaje = ($row['total'] / $total) * 100;
                break;
            case 'Crecimiento':
                $crecimiento_sal_percentaje = ($row['total'] / $total) * 100;
                break;
            case 'Finalizacion':
                $finalizacion_sal_percentaje = ($row['total'] / $total) * 100;
                break;
        }
    }
}

// Calculos Graficos de Melaza
$sql = "SELECT vh_melaza_etapa, SUM(vh_melaza_racion * vh_melaza_costo) AS total
        FROM vh_melaza
        GROUP BY vh_melaza_etapa";

$result = mysqli_query($conn, $sql);

$inicio_melaza_percentaje = 0;
$crecimiento_melaza_percentaje = 0;
$finalizacion_melaza_percentaje = 0;
$total = 0; // Initialize total variable

if ($result) {
    // First pass to calculate total
    while ($row = mysqli_fetch_assoc($result)) {
        $total += $row['total']; // Accumulate total
    }
    // Reset result pointer to fetch data again
    mysqli_data_seek($result, 0);

    // Second pass to calculate percentages
    while ($row = mysqli_fetch_assoc($result)) {
        switch ($row['vh_melaza_etapa']) {
            case 'Inicio':
                $inicio_melaza_percentaje = ($row['total'] / $total) * 100;
                break;
            case 'Crecimiento':
                $crecimiento_melaza_percentaje = ($row['total'] / $total) * 100;
                break;
            case 'Finalizacion':
                $finalizacion_melaza_percentaje = ($row['total'] / $total) * 100;
                break;
        }
    }
}

// ********** Indices de Salud *****************
// AFTOSA
$total_animals = 0;
$total_aftosa_vaccinated = 0;
$aftosa_vaccinated_percentage = 0;

// SQL query to count unique tagids from vacuno table
$sql_animals = "
    SELECT COUNT(DISTINCT tagid) AS total_animals
    FROM vacuno
";

// Execute the query for total animals
$result_animals = mysqli_query($conn, $sql_animals);
if ($result_animals) {
    $row = mysqli_fetch_assoc($result_animals);
    $total_animals = $row['total_animals'];
}

// SQL query to count distinct vh_aftosa_tagid from vh_aftosa table
$sql_aftosa = "
    SELECT COUNT(DISTINCT vh_aftosa_tagid) AS total_aftosa_vaccinated
    FROM vh_aftosa
";

// Execute the query for total aftosa vaccinated
$result_aftosa = mysqli_query($conn, $sql_aftosa);
if ($result_aftosa) {
    $row = mysqli_fetch_assoc($result_aftosa);
    $total_aftosa_vaccinated = $row['total_aftosa_vaccinated'];
}

// Calculate the percentage of animals vaccinated with aftosa
if ($total_animals > 0) {
    $aftosa_vaccinated_percentage = ($total_aftosa_vaccinated / $total_animals) * 100;
} else {
    $aftosa_vaccinated_percentage = 0; // Avoid division by zero
}

// BRUCELOSIS
$total_animals = 0;
$total_brucelosis_vaccinated = 0;
$brucelosis_vaccinated_percentage = 0;

// SQL query to count unique tagids from vacuno table
$sql_animals = "
    SELECT COUNT(DISTINCT tagid) AS total_animals
    FROM vacuno
";

// Execute the query for total animals
$result_animals = mysqli_query($conn, $sql_animals);
if ($result_animals) {
    $row = mysqli_fetch_assoc($result_animals);
    $total_animals = $row['total_animals'];
}

// SQL query to count distinct vh_brucelosis_tagid from vh_brucelosis table
$sql_brucelosis = "
    SELECT COUNT(DISTINCT vh_brucelosis_tagid) AS total_brucelosis_vaccinated
    FROM vh_brucelosis
";

// Execute the query for total brucelosis vaccinated
$result_brucelosis = mysqli_query($conn, $sql_brucelosis);
if ($result_brucelosis) {
    $row = mysqli_fetch_assoc($result_brucelosis);
    $total_brucelosis_vaccinated = $row['total_brucelosis_vaccinated'];
}

// Calculate the percentage of animals vaccinated with brucelosis
if ($total_animals > 0) {
    $brucelosis_vaccinated_percentage = ($total_brucelosis_vaccinated / $total_animals) * 100;
} else {
    $brucelosis_vaccinated_percentage = 0;
}

// IBR
$total_animals = 0;
$total_ibr_vaccinated = 0;
$ibr_vaccinated_percentage = 0;

// SQL query to count unique tagids from vacuno table
$sql_animals = "
    SELECT COUNT(DISTINCT tagid) AS total_animals
    FROM vacuno
";

// Execute the query for total animals
$result_animals = mysqli_query($conn, $sql_animals);
if ($result_animals) {
    $row = mysqli_fetch_assoc($result_animals);
    $total_animals = $row['total_animals'];
}

// SQL query to count distinct vh_ibr_tagid from vh_ibr table
$sql_ibr = "
    SELECT COUNT(DISTINCT vh_ibr_tagid) AS total_ibr_vaccinated
    FROM vh_ibr
";

// Execute the query for total ibr vaccinated
$result_ibr = mysqli_query($conn, $sql_ibr);
if ($result_ibr) {
    $row = mysqli_fetch_assoc($result_ibr);
    $total_ibr_vaccinated = $row['total_ibr_vaccinated'];
}

// Calculate the percentage of animals vaccinated with ibr
if ($total_animals > 0) {
    $ibr_vaccinated_percentage = ($total_ibr_vaccinated / $total_animals) * 100;
} else {
    $ibr_vaccinated_percentage = 0; // Avoid division by zero
}

// Carbunco
$total_animals = 0;
$total_carbunco_vaccinated = 0;
$carbunco_vaccinated_percentage = 0;

// SQL query to count unique tagids from vacuno table
$sql_animals = "
    SELECT COUNT(DISTINCT tagid) AS total_animals
    FROM vacuno
";

// Execute the query for total animals
$result_animals = mysqli_query($conn, $sql_animals);
if ($result_animals) {
    $row = mysqli_fetch_assoc($result_animals);
    $total_animals = $row['total_animals'];
}

// SQL query to count distinct vh_carbunco_tagid from vh_carbunco table
$sql_carbunco = "
    SELECT COUNT(DISTINCT vh_carbunco_tagid) AS total_carbunco_vaccinated
    FROM vh_carbunco
";

// Execute the query for total carbunco vaccinated
$result_carbunco = mysqli_query($conn, $sql_carbunco);
if ($result_carbunco) {
    $row = mysqli_fetch_assoc($result_carbunco);
    $total_carbunco_vaccinated = $row['total_carbunco_vaccinated'];
}

// Calculate the percentage of animals vaccinated with carbunco
if ($total_animals > 0) {
    $carbunco_vaccinated_percentage = ($total_carbunco_vaccinated / $total_animals) * 100;
} else {
    $carbunco_vaccinated_percentage = 0;
}

// CBR
$total_animals = 0;
$total_cbr_vaccinated = 0;
$cbr_vaccinated_percentage = 0;

// SQL query to count unique tagids from vacuno table
$sql_animals = "
    SELECT COUNT(DISTINCT tagid) AS total_animals
    FROM vacuno
";

// Execute the query for total animals
$result_animals = mysqli_query($conn, $sql_animals);
if ($result_animals) {
    $row = mysqli_fetch_assoc($result_animals);
    $total_animals = $row['total_animals'];
}

// SQL query to count distinct vh_cbr_tagid from vh_cbr table
$sql_cbr = "
    SELECT COUNT(DISTINCT vh_cbr_tagid) AS total_cbr_vaccinated
    FROM vh_cbr
";

// Execute the query for total CBR vaccinated
$result_cbr = mysqli_query($conn, $sql_cbr);
if ($result_cbr) {
    $row = mysqli_fetch_assoc($result_cbr);
    $total_cbr_vaccinated = $row['total_cbr_vaccinated'];
}

// Calculate the percentage of animals vaccinated with CBR
if ($total_animals > 0) {
    $cbr_vaccinated_percentage = ($total_cbr_vaccinated / $total_animals) * 100;
} else {
    $cbr_vaccinated_percentage = 0;
}

// Garrapatas
$total_animals = 0;
$total_garrapatas_vaccinated = 0;
$garrapatas_vaccinated_percentage = 0;

// SQL query to count unique tagids from vacuno table
$sql_animals = "
    SELECT COUNT(DISTINCT tagid) AS total_animals
    FROM vacuno
";

// Execute the query for total animals
$result_animals = mysqli_query($conn, $sql_animals);
if ($result_animals) {
    $row = mysqli_fetch_assoc($result_animals);
    $total_animals = $row['total_animals'];
}

// SQL query to count distinct vh_garrapatas_tagid from vh_garrapatas table
$sql_garrapatas = "
    SELECT COUNT(DISTINCT vh_garrapatas_tagid) AS total_garrapatas_vaccinated
    FROM vh_garrapatas
";

// Execute the query for total CBR vaccinated
$result_garrapatas = mysqli_query($conn, $sql_garrapatas);
if ($result_garrapatas) {
    $row = mysqli_fetch_assoc($result_garrapatas);
    $total_garrapatas_vaccinated = $row['total_garrapatas_vaccinated'];
}

// Calculate the percentage of animals vaccinated with garrapatas
if ($total_animals > 0) {
    $garrapatas_vaccinated_percentage = ($total_garrapatas_vaccinated / $total_animals) * 100;
} else {
    $garrapatas_vaccinated_percentage = 0;
}

// Mastitis
$total_animals = 0;
$total_mastitis_vaccinated = 0;
$mastitis_vaccinated_percentage = 0;

// SQL query to count unique tagids from vacuno table
$sql_animals = "
    SELECT COUNT(DISTINCT tagid) AS total_animals
    FROM vacuno
";

// Execute the query for total animals
$result_animals = mysqli_query($conn, $sql_animals);
if ($result_animals) {
    $row = mysqli_fetch_assoc($result_animals);
    $total_animals = $row['total_animals'];
}

// SQL query to count distinct vh_mastitis_tagid from vh_mastitis table
$sql_mastitis = "
    SELECT COUNT(DISTINCT vh_mastitis_tagid) AS total_mastitis_vaccinated
    FROM vh_mastitis
";

// Execute the query for total mastitis vaccinated
$result_mastitis = mysqli_query($conn, $sql_mastitis);
if ($result_mastitis) {
    $row = mysqli_fetch_assoc($result_mastitis);
    $total_mastitis_vaccinated = $row['total_mastitis_vaccinated'];
}

// Calculate the percentage of animals vaccinated with mastitis
if ($total_animals > 0) {
    $mastitis_vaccinated_percentage = ($total_mastitis_vaccinated / $total_animals) * 100;
} else {
    $mastitis_vaccinated_percentage = 0;
}
// Parasitos
$total_animals = 0;
$total_parasitos_vaccinated = 0;
$parasitos_vaccinated_percentage = 0;

// SQL query to count unique tagids from vacuno table
$sql_animals = "
    SELECT COUNT(DISTINCT tagid) AS total_animals
    FROM vacuno
";

// Execute the query for total animals
$result_animals = mysqli_query($conn, $sql_animals);
if ($result_animals) {
    $row = mysqli_fetch_assoc($result_animals);
    $total_animals = $row['total_animals'];
}

// SQL query to count distinct vh_parasitos_tagid from vh_parasitos table
$sql_parasitos = "
    SELECT COUNT(DISTINCT vh_parasitos_tagid) AS total_parasitos_vaccinated
    FROM vh_parasitos
";

// Execute the query for total Parasitos vaccinated
$result_parasitos = mysqli_query($conn, $sql_parasitos);
if ($result_parasitos) {
    $row = mysqli_fetch_assoc($result_parasitos);
    $total_parasitos_vaccinated = $row['total_parasitos_vaccinated'];
}

// Calculate the percentage of animals vaccinated with parasitos
if ($total_animals > 0) {
    $parasitos_vaccinated_percentage = ($total_parasitos_vaccinated / $total_animals) * 100;
} else {
    $parasitos_vaccinated_percentage = 0;
}

// ********** Indices de Reproduccion **********
// Initialize counters
$counter1 = 0;
$counter2 = 0;
$counter3 = 0;
// SQL query to join vh_gestacion and vh_inseminacion
$sql = "
    SELECT 
        g.vh_gestacion_tagid, 
        g.vh_gestacion_fecha, 
        i.vh_inseminacion_fecha, 
        i.vh_inseminacion_numero
    FROM 
        vh_gestacion g
    JOIN 
        vh_inseminacion i ON g.vh_gestacion_tagid = i.vh_inseminacion_tagid
    WHERE 
        g.vh_gestacion_numero = i.vh_inseminacion_numero AND
        DATEDIFF(g.vh_gestacion_fecha, i.vh_inseminacion_fecha) < 31
";

// Execute the query
$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        switch ($row['vh_inseminacion_numero']) {
            case 1:
                $counter1++;
                break;
            case 2:
                $counter2++;
                break;
            case 3:
                $counter3++;
                break;
        }
    }
}

$porcentaje_1era_inseminacion = $counter1/($counter1 + $counter2 + $counter3)*100;
$porcentaje_2da_inseminacion = $counter2/($counter1 + $counter2 + $counter3)*100;
$porcentaje_3era_inseminacion = $counter3/($counter1 + $counter2 + $counter3)*100;


// Initialize variables
$totalDays = 0;
$count = 0;

// SQL query to join vh_parto and vh_gestacion
$sql = "
    SELECT 
        DATEDIFF(p.vh_parto_fecha, g.vh_gestacion_fecha) AS days_difference
    FROM 
        vh_parto p
    JOIN 
        vh_gestacion g ON p.vh_parto_tagid = g.vh_gestacion_tagid
    WHERE 
        p.vh_parto_numero = g.vh_gestacion_numero
";

// Execute the query
$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $totalDays += $row['days_difference']; // Accumulate total days
        $count++; // Increment count of entries
    }
}

// Calculate average if count is greater than 0 to avoid division by zero
$averageDays = $count > 0 ? $totalDays / $count : 0;

// Now you can use $averageDays as needed

// Initialize variables
$totalEmptyDays = 0;
$count = 0;

// SQL query to calculate empty days
$sql = "
    SELECT 
        g.vh_gestacion_fecha,
        g.vh_gestacion_numero,
        p.vh_parto_numero,
        COALESCE(p.vh_parto_fecha, '0000-00-00') AS parto_fecha
    FROM 
        vh_gestacion g
    RIGHT JOIN 
        vh_parto p ON g.vh_gestacion_tagid = p.vh_parto_tagid 
        AND g.vh_gestacion_numero - 1 = p.vh_parto_numero
";

// Execute the query
$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Calculate the difference in days
        if ($row['parto_fecha'] !== '0000-00-00') {
            $daysDifference = abs((strtotime($row['vh_gestacion_fecha']) - strtotime($row['parto_fecha'])) / (60 * 60 * 24));
            $totalEmptyDays += $daysDifference; // Accumulate total empty days
            $count++; // Increment count of entries
        } else {
            // If there is no previous birth, empty days are considered zero
            $totalEmptyDays += 0; // This line is optional as it does not change the total
            $count++; // Still count this entry
        }
    }
}

// Calculate average if count is greater than 0 to avoid division by zero
$averageEmptyDays = $count > 0 ? $totalEmptyDays / $count : 0;

// Now you can use $averageEmptyDays as needed

// Initialize variables
$totalDaysDifference = 0;
$count = 0;

// SQL query to calculate days between consecutive births
$sql = "
    SELECT 
        p1.vh_parto_fecha AS fecha_n,
        p2.vh_parto_fecha AS fecha_n_plus_1
    FROM 
        vh_parto p1
    JOIN 
        vh_parto p2 ON p1.vh_parto_tagid = p2.vh_parto_tagid 
        AND p2.vh_parto_numero = p1.vh_parto_numero + 1
";

// Execute the query
$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Calculate the difference in days
        $daysDifference = abs((strtotime($row['fecha_n_plus_1']) - strtotime($row['fecha_n'])) / (60 * 60 * 24));
        $totalDaysDifference += $daysDifference; // Accumulate total days difference
        $count++; // Increment count of entries
    }
}

// Calculate average if count is greater than 0 to avoid division by zero
$averageDaysDifference = $count > 0 ? $totalDaysDifference / $count : 0;

// Now you can use $averageDaysDifference as needed

// ********** Indices Otros ********************


?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Veterinario IA</title>
<!-- Link to the Favicon -->
<link rel="icon" href="images/Ganagram_icono.ico" type="image/x-icon">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<!--Bootstrap 5 Css -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">



<!-- Include Chart.js and Chart.js DataLabels Plugin -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<!-- Add these in the <head> section, after your existing CSS/JS links -->

<!-- Place these in the <head> section in this exact order -->

<!-- jQuery Core (main library) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">

<!-- DataTables JavaScript -->
<script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>

<!-- DataTables Buttons CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<!-- DataTables Buttons JS -->
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
<script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Add these in the <head> section, after your existing DataTables CSS/JS -->
<!-- DataTables Buttons CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<!-- DataTables Buttons JS -->
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<!-- ECharts -->
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
<!-- Custom Modal Styles -->
<link rel="stylesheet" href="./vacuno.css">

<style>
    /* Modal Styling */
    .modal-content {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }
    
    .modal-header {
        background: linear-gradient(to right, #28a745, #20c997);
        color: white;
        border-bottom: none;
        padding: 1.5rem;
    }
    
    .modal-header .modal-title {
        font-weight: 600;
        font-size: 1.25rem;
    }
    
    .modal-header .btn-close {
        color: white;
        opacity: 0.8;
        transition: opacity 0.3s;
        filter: brightness(0) invert(1);
    }
    
    .modal-header .btn-close:hover {
        opacity: 1;
    }
    
    .modal-body {
        padding: 1.75rem;
        background-color: #f8f9fa;
    }
    
    .modal-footer {
        border-top: none;
        padding: 1rem 1.75rem 1.5rem;
        background-color: #f8f9fa;
    }
    
    /* Form Elements */
    .modal .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    
    .modal .form-control {
        border-radius: 0.375rem;
        border: 1px solid #ced4da;
        padding: 0.75rem 1rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    
    .modal .form-control:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
    }
    
    .modal .form-control:hover:not(:focus) {
        border-color: #adb5bd;
    }
    
    /* Buttons */
    .modal .btn {
        padding: 0.5rem 1.5rem;
        font-weight: 500;
        border-radius: 0.375rem;
        transition: all 0.3s;
    }
    
    .modal .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }
    
    .modal .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
    }
    
    .modal .btn-success:active {
        transform: translateY(0);
        box-shadow: none;
    }
    
    .modal .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }
    
    .modal .btn-secondary:hover {
        background-color: #5a6268;
        border-color: #545b62;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(108, 117, 125, 0.3);
    }
    
    .modal .btn-secondary:active {
        transform: translateY(0);
        box-shadow: none;
    }
    
    /* Animation */
    .modal.fade .modal-dialog {
        transform: scale(0.9);
        opacity: 0;
        transition: transform 0.3s ease, opacity 0.3s ease;
    }
    
    .modal.show .modal-dialog {
        transform: scale(1);
        opacity: 1;
    }
    
    /* Modal Backdrop */
    .modal-backdrop.show {
        opacity: 0.7;
        backdrop-filter: blur(3px);
    }
    
    /* Input Group */
    .input-group {
        margin-bottom: 1rem;
    }
    
    /* Input Group Text */
    .input-group-text {
        background-color: #f8f9fa;
        border-color: #ced4da;
        color: #28a745;
    }
    
    /* Focused Form Group Effect */
    .modal .form-control:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
    }
    
    /* Modal Highlight Animation on Open */
    @keyframes modalHighlight {
        0% {
            box-shadow: 0 0 0 rgba(40, 167, 69, 0);
        }
        50% {
            box-shadow: 0 0 30px rgba(40, 167, 69, 0.3);
        }
        100% {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
    }
    
    .modal.show .modal-content {
        animation: modalHighlight 0.5s ease forwards;
    }
    
    /* Hover effect for input groups */
    .modal .input-group:hover .input-group-text {
        background-color: #e9ecef;
        transition: background-color 0.3s;
    }
    
    /* Readonly fields styling */
    .modal input[readonly] {
        background-color: #e9ecef;
        cursor: not-allowed;
    }
    
    /* Form validation styles */
    .modal .form-control:invalid:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
    }
    
    /* Modal title icon */
    .modal-title i {
        margin-right: 8px;
    }

    /* Back to Top Button Styling */
    .back-to-top {
        position: fixed;
        bottom: 25px;
        right: 25px;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        cursor: pointer;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 1000;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }

    .back-to-top.visible {
        opacity: 1;
        visibility: visible;
    }

    .back-to-top:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
    }

    .back-to-top:active {
        transform: translateY(0);
    }

    @media (max-width: 768px) {
        .back-to-top {
            bottom: 15px;
            right: 15px;
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }
    }

.button-label {
    display: block;
    text-align: center;
    font-size: 0.7rem;
    width: 100%;
}

    .error-message {
        background-color: #ffebee;
        color: #c62828;
        padding: 10px;
        margin: 10px 0;
        border-radius: 20px;
        border-bottom-left-radius: 5px;
    }

    .full-width-button {
        width: 100% !important;
        display: block !important;
        box-sizing: border-box !important;
    }

    /* Initial system message style */
</style>

</head>
<body>
<!-- Navigation Title -->

<!-- Icon Navigation Buttons -->

<div class="container nav-icons-container">
    <div class="icon-button-container">
        <button onclick="window.location.href='../inicio.php'" class="icon-button">
            <img src="./images/Ganagram_New_Logo-png.png" alt="Inicio" class="nav-icon">
        </button>
        <span class="button-label">INICIO</span>
    </div>
    
    <div class="icon-button-container">
        <button onclick="window.location.href='./inventario_vacuno.php'" class="icon-button">
            <img src="./images/robot-de-chat.png" alt="Inicio" class="nav-icon">
        </button>
        <span class="button-label">VETERINARIO IA</span>
    </div>

    <div class="icon-button-container">
        <button onclick="window.location.href='./vacuno_registros.php'" class="icon-button">
            <img src="./images/registros.png" alt="Inicio" class="nav-icon">
        </button>
        <span class="button-label">REGISTROS</span>
    </div>
    
    <div class="icon-button-container">
        <button onclick="window.location.href='./vacuno_feria.php'" class="icon-button">
            <img src="./images/feria.png" alt="Inicio" class="nav-icon">
        </button>
        <span class="button-label">FERIA</span>
    </div>

    
    <div class="icon-button-container">
            <button onclick="window.location.href='./vacuno_configuracion.php'" class="icon-button">
                <img src="./images/configuracion.png" alt="Inicio" class="nav-icon">
            </button>
            <span class="button-label">CONFIG</span>
        </div>

</div>


<style>
    .chart-container {
        position: relative;
        height: min(250px, 50vh);
        width: 100%;
        margin: auto;
    }
</style>

<!-- Scroll Icons Container -->
<div class="container scroll-icons-container">
    <button class="btn btn-outline-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#produccion" 
        data-tooltip="Produccion"
        aria-expanded="false"
        aria-controls="produccion">
        <img src="./images/bascula-de-comestibles.png" alt="Produccion" class="nav-icon">
        <span class="button-label">PRODUCCION</span>
    </button>

    <button class="btn btn-outline-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#alimentacion" 
        data-tooltip="Alimentacion"
        aria-expanded="false"
        aria-controls="alimentacion">
        <img src="./images/concentrado.png" alt="Alimentacion" class="nav-icon">
        <span class="button-label">ALIMENTACION</span>
    </button>

    <button class="btn btn-outline-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#salud" 
        data-tooltip="Salud"
        aria-expanded="false"
        aria-controls="salud">
        <img src="./images/proteger.png" alt="Salud" class="nav-icon">
        <span class="button-label">SALUD</span>
    </button>
    
    <button class="btn btn-outline-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#reproduccion" 
        data-tooltip="Reproduccion"
        aria-expanded="false"
        aria-controls="reproduccion">
        <img src="./images/matriz.png" alt="Reproduccion" class="nav-icon">
        <span class="button-label">REPRODUCCION</span>
    </button>
</div>

<!-- Indices de Produccion -->

<!-- Indices Produccion Leche-->
<h3  class="container mt-4 text-white" id="produccion">
INDICES PRODUCCION LECHE
</h3>


<div style="max-width: 1300px; margin: 40px auto;">
    <h2 style="text-align: center;">Leche: Producción Mensual Promedio</h2>
    <canvas id="avgLecheChart" width="600" height="400"></canvas>
</div>
<div style="text-align: center; margin: 20px;">
    <button id="exportAvgLechePdf" class="btn btn-success">Exportar a PDF</button>
</div>
<div style="max-width: 1300px; margin: 40px auto;">
    <h2 style="text-align: center;">Leche: Ingresos Mensuales y Acumulativos</h2>
    <canvas id="monthlyRevenueChart" width="600" height="400"></canvas>
</div>
<div style="text-align: center; margin: 20px;">
    <button id="exportPdf" class="btn btn-success">Exportar a PDF</button>
</div>
<div class="container d-flex justify-content-center flex-wrap">      
      <figure id="chart-lactancia"></figure>
      <figure id="chart-enordeno"></figure>
</div>

<!-- Indices Produccion Carne-->
<h3  class="container mt-4 text-white" id="carne">
INDICES PRODUCCION CARNE
</h3>

<div style="max-width: 1300px; margin: 40px auto;">

    <h2 style="text-align: center;">Carne: Peso Promedio</h2>
    <canvas id="monthlyAverageWeightChart" width="600" height="400"></canvas>
</div>

<div style="text-align: center; margin: 20px;">
    <button id="exportAvgWeightPdf" class="btn btn-success">Exportar a PDF (Peso promedio)</button>
</div>

<div style="max-width: 1300px; margin: 40px auto;">
    <h2 style="text-align: center;">Carne: Ingresos Mensuales</h2>
    <canvas id="monthlyAverageRevenueChart" width="600" height="400"></canvas>
</div>

<div style="text-align: center; margin: 20px;">
    <button id="exportAvgRevenuePdf" class="btn btn-success">Exportar a PDF (Ingresos promedio)</button>
</div>

<div class="container d-flex justify-content-center flex-wrap">      
      <figure id="chart-aumento-mensual-peso"></figure>
      <figure id="chart-Carne-Alimento"></figure>
</div>

<!-- Indices de Alimentacion -->
<h3  class="container mt-4 text-white" id="alimentacion">
INDICES ALIMENTACION
</h3>

<!-- Alimento Concentrado-->

<h2 class="container d-flex justify-content-center flex-wrap">Concentrado</h2>

<div class="container d-flex justify-content-center flex-wrap">           
  <figure id="chart-becerros-concentrado"></figure>
  <figure id="chart-novillos-concentrado"></figure>
  <figure id="chart-adultos-concentrado"></figure>
</div>
<!-- Sal-->

<h2 class="container d-flex justify-content-center flex-wrap">Sal</h2>

<div class="container d-flex justify-content-center flex-wrap">      
      <figure id="chart-becerros-sal"></figure>
      <figure id="chart-novillos-sal"></figure>
      <figure id="chart-adultos-sal"></figure>
</div>
<!-- Indices Melaza-->

<h2 class="container d-flex justify-content-center flex-wrap">Melaza</h2>

<div class="container d-flex justify-content-center flex-wrap">      
      <figure id="chart-becerros-melaza"></figure>
      <figure id="chart-novillos-melaza"></figure>
      <figure id="chart-adultos-melaza"></figure>
</div>

<!-- Indices de Salud -->
<h3  class="container mt-4 text-white" id="salud">
INDICES SALUD
</h3>

<!-- Indices Vacunas-->
<h2 class="container d-flex justify-content-center flex-wrap">Vacunas</h2>

<div class="container d-flex justify-content-center flex-wrap">
      <figure id="chart-aftosa"></figure>
      <figure id="chart-brucelosis"></figure>
      <figure id="chart-ibr"></figure>
</div>
<div class="container d-flex justify-content-center flex-wrap">
<figure id="chart-cbr"></figure>
      <figure id="chart-carbunco"></figure>
</div>
<!-- Indices Baños-->

<h2 class="container d-flex justify-content-center flex-wrap">Baños</h2>

<div class="container d-flex justify-content-center flex-wrap">      
    <figure id="chart-parasitos"></figure>
    <figure id="chart-garrapatas"></figure>
</div>
<!-- Indices Tratamientos-->

<h2 class="container d-flex justify-content-center flex-wrap">Tratamientos</h2>

<div class="container d-flex justify-content-center flex-wrap">
      <figure id="chart-mastitis"></figure>
</div>

<!-- Indices de Reproduccion -->
<h3  class="container mt-4 text-white" id="reproduccion">
INDICES REPRODUCCION
</h3>

<!-- Indices Preñez-->
<h2 class="container d-flex justify-content-center flex-wrap">Preñez</h2>

<div class="container d-flex justify-content-center flex-wrap">      
      <figure id="chart-1er-celo"></figure>
      <figure id="chart-2do-celo"></figure>
      <figure id="chart-3er-celo"></figure>
</div>
<!-- Indices Gestacion-->

<h2 class="container d-flex justify-content-center flex-wrap">Gestacion</h2>

<div class="container d-flex justify-content-center flex-wrap">      

      <figure id="chart-dias-gestacion"></figure>
      <figure id="chart-dias-vacios"></figure>

</div>
<!-- Indices Paricion-->

<h2 class="container d-flex justify-content-center flex-wrap">Paricion</h2>

<div class="container d-flex justify-content-center flex-wrap">      
      <figure id="chart-dias-entre-partos"></figure>
      <figure id="chart-partos-anuales"></figure>
</div>

<!-- Librerias -->
<!-- Bootstrap  -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
<!-- Librerias -->
<!-- Bootstrap  -->
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<!-- Popper Js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.10.2/umd/popper.min.js"></script>
<!-- para usar botones en datatables JS -->  
    <script src="https://ganagram.com/ganagram/crud/datatables/Buttons-1.5.6/js/dataTables.buttons.min.js"></script>  
    <script src="https://ganagram.com/ganagram/crud/datatables/JSZip-2.5.0/jszip.min.js"></script>    
    <script src="https://ganagram.com/ganagram/crud/datatables/pdfmake-0.1.36/pdfmake.min.js"></script>    
    <script src="https://ganagram.com/ganagram/crud/datatables/pdfmake-0.1.36/vfs_fonts.js"></script>
    <script src="https://ganagram.com/ganagram/crud/datatables/Buttons-1.5.6/js/buttons.html5.min.js"></script>
<!-- Ion Icon Js -->
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>    
<!-- Custom Menu Js -->
<script src="https://ganagram.com/ganagram/html/js/menu.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- html2canvas -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<!-- jsPDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<!-- Scroll to Section-->

<script>
// Add event listeners to all scroll buttons
document.querySelectorAll('.scroll-icons-container button').forEach(button => {
    button.addEventListener('click', function() {
        // Get the target section ID from data-bs-target attribute
        const targetId = this.getAttribute('data-bs-target');
        const targetElement = document.getElementById(targetId.substring(1)); // Remove the # from the ID

        if (targetElement) {
            // Smooth scroll to the target section
            targetElement.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });

            // If using Bootstrap collapse, toggle it
            const bsCollapse = new bootstrap.Collapse(targetElement, {
                toggle: true
            });
        }
    });
});
</script>

<!-- Back to top button -->
<button id="backToTop" class="back-to-top" onclick="scrollToTop()" title="Volver arriba">
    <div class="arrow-up"><i class="fa-solid fa-arrow-up"></i></div>
</button>

<script>
window.onscroll = function() {
    const backToTopButton = document.getElementById("backToTop");
    if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
        backToTopButton.style.display = "flex";
    } else {
        backToTopButton.style.display = "none";
    }
};

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}
</script>


<!-- Indices de Produccion -->

<!-- Average Leche Weight Chart -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var ctxAvgLeche = document.getElementById('avgLecheChart').getContext('2d');
        var avgLecheChart = new Chart(ctxAvgLeche, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($avgLecheLabels); ?>,
                datasets: [{
                    label: 'Promedio Produccion Leche ',
                    data: <?php echo json_encode($avgLecheData); ?>,
                    borderColor: '#FF6384',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    fill: true,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y;
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Producción Promedio de Leche Mensual'
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Mes (Año-Mes)'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Promedio Produccion Leche'
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
<!-- Monthly Leche revenue Chart -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var ctxMonthlyRevenue = document.getElementById('monthlyRevenueChart').getContext('2d');
        var monthlyRevenueChart = new Chart(ctxMonthlyRevenue, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($revenueLecheLabels); ?>,
                datasets: [{
                    label: 'Ingresos Totales (USD)',
                    data: <?php echo json_encode($revenueLecheData); ?>,
                    borderColor: '#4CAF50',
                    backgroundColor: 'rgba(76, 175, 80, 0.2)',
                    fill: true,
                    tension: 0.1
                },
                {
                    label: 'Ingresos Cumulativos (USD)',
                    data: <?php echo json_encode($cumulativeRevenueData); ?>,
                    borderColor: '#FF6384',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    fill: true,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': $' + context.parsed.y;
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Ingresos Mensuales y Acumulativos Leche'
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Mes (Año-Mes)'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Ingresos (USD)'
                        },
                        beginAtZero: true
                    }
                }
            }
        });

        document.getElementById('exportPdf').addEventListener('click', function() {
            // Use html2canvas to capture the chart
            html2canvas(document.getElementById('monthlyRevenueChart')).then(function(canvas) {
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF('l', 'pt', 'a4'); // Landscape orientation
                const imgData = canvas.toDataURL('image/png');
                const imgWidth = 280; // Adjust width as needed
                const pageHeight = pdf.internal.pageSize.height;
                const imgHeight = (canvas.height * imgWidth) / canvas.width;
                let heightLeft = imgHeight;

                let position = 10;

                // Add image to PDF and handle page breaks if necessary
                pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;

                while (heightLeft >= 0) {
                    position = heightLeft - imgHeight;
                    pdf.addPage();
                    pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
                    heightLeft -= pageHeight;
                }

                // Save the PDF
                pdf.save('monthly_revenue_chart.pdf');
            }).catch(function(error) {
                console.error('Error capturing the chart:', error);
            });
        });
    });
</script>
<!-- Average Leche Chart -->
<script>
    document.getElementById('exportAvgLechePdf').addEventListener('click', function() {
        // Use html2canvas to capture the avgLecheChart
        html2canvas(document.getElementById('avgLecheChart')).then(function(canvas) {
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF('l', 'pt', 'a4'); // Landscape orientation
            const imgData = canvas.toDataURL('image/png');
            const imgWidth = 280; // Adjust width as needed
            const pageHeight = pdf.internal.pageSize.height;
            const imgHeight = (canvas.height * imgWidth) / canvas.width;
            let heightLeft = imgHeight;

            let position = 10;

            // Add image to PDF and handle page breaks if necessary
            pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;

            while (heightLeft >= 0) {
                position = heightLeft - imgHeight;
                pdf.addPage();
                pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;
            }

            // Save the PDF
            pdf.save('avg_leche_chart.pdf');
        }).catch(function(error) {
            console.error('Error capturing the chart:', error);
        });
    });
</script>
<!-- Average Weight Chart -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var ctxMonthlyAverageWeight = document.getElementById('monthlyAverageWeightChart').getContext('2d');
        var monthlyAverageWeightChart = new Chart(ctxMonthlyAverageWeight, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($revenuePesoLabels); ?>,
                datasets: [{
                    label: 'Promedio de Peso (Kg)',
                    data: <?php echo json_encode($averagePesoRevenueData); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y + ' Kg';
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Peso Promedio Mensual'
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Mes (Año-Mes)'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Promedio de Peso (Kg)'
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
<!-- Monthly Average Weight Chart -->
<script>
    document.getElementById('exportAvgWeightPdf').addEventListener('click', function() {
        // Use html2canvas to capture the monthlyAverageWeightChart
        html2canvas(document.getElementById('monthlyAverageWeightChart')).then(function(canvas) {
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF('l', 'pt', 'a4'); // Landscape orientation
            const imgData = canvas.toDataURL('image/png');
            const imgWidth = 280; // Adjust width as needed
            const pageHeight = pdf.internal.pageSize.height;
            const imgHeight = (canvas.height * imgWidth) / canvas.width;
            let heightLeft = imgHeight;

            let position = 10;

            // Add image to PDF and handle page breaks if necessary
            pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;

            while (heightLeft >= 0) {
                position = heightLeft - imgHeight;
                pdf.addPage();
                pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;
            }

            // Save the PDF
            pdf.save('promedio_mensual_peso_chart.pdf');
        }).catch(function(error) {
            console.error('Error capturing the chart:', error);
        });
    });
</script>
<!-- Monthly Average Revenue Chart -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var ctxMonthlyAverageRevenue = document.getElementById('monthlyAverageRevenueChart').getContext('2d');
        var monthlyAverageRevenueChart = new Chart(ctxMonthlyAverageRevenue, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($revenueLabels); ?>,
                datasets: [{
                    label: 'Promedio de Ingresos (USD)',
                    data: <?php echo json_encode($averageRevenueData); ?>,
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': $' + context.parsed.y;
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Ingresos Promedio Mensual'
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Mes (Año-Mes)'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Promedio de Ingresos (USD)'
                        },
                        beginAtZero: true
                    }
                }
            }
        });

        // Export to PDF functionality
        document.getElementById('exportAvgRevenuePdf').addEventListener('click', function() {
            html2canvas(document.getElementById('monthlyAverageRevenueChart')).then(function(canvas) {
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF('l', 'pt', 'a4'); // Landscape orientation
                const imgData = canvas.toDataURL('image/png');
                const imgWidth = 800; // Adjust width as needed
                const pageHeight = pdf.internal.pageSize.height;
                const imgHeight = (canvas.height * imgWidth) / canvas.width;
                let heightLeft = imgHeight;

                let position = 10;

                // Add image to PDF and handle page breaks if necessary
                pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;

                while (heightLeft >= 0) {
                    position = heightLeft - imgHeight;
                    pdf.addPage();
                    pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
                    heightLeft -= pageHeight;
                }

                // Save the PDF
                pdf.save('promedio_mensual_ingresos_chart.pdf');
            }).catch(function(error) {
                console.error('Error capturing the chart:', error);
            });
        });
    });
</script>
<!-- INDICE: Dias de Lactancia -->
<script>
var dom = document.getElementById('chart-lactancia');
var myChart = echarts.init(dom, null, {
  renderer: 'canvas',
  useDirtyRect: false
});
var app = {};
var option;
option = {
  series: [
    {
      type: 'gauge',
      startAngle: 180,
      endAngle: 0,
      min: 100,
      max: 200,
      splitNumber: 8,
      itemStyle: {
        color: '#215429',
        shadowColor: 'rgba(0,138,255,0.45)',
        shadowBlur: 10,
        shadowOffsetX: 2,
        shadowOffsetY: 2
      },
      progress: {
        show: true,
        roundCap: true,
        width: 10
      },
      pointer: {
        icon: 'path://M2090.36389,615.30999 L2090.36389,615.30999 C2091.48372,615.30999 2092.40383,616.194028 2092.44859,617.312956 L2096.90698,728.755929 C2097.05155,732.369577 2094.2393,735.416212 2090.62566,735.56078 C2090.53845,735.564269 2090.45117,735.566014 2090.36389,735.566014 L2090.36389,735.566014 C2086.74736,735.566014 2083.81557,732.63423 2083.81557,729.017692 C2083.81557,728.930412 2083.81732,728.84314 2083.82081,728.755929 L2088.2792,617.312956 C2088.32396,616.194028 2089.24407,615.30999 2090.36389,615.30999 Z',
        length: '75%',
        width: 10,
        offsetCenter: [0, '5%']
      },
      axisLine: {
        roundCap: true,
        lineStyle: {
          width: 2
        }
      },
      axisTick: {
        splitNumber: 2,
        lineStyle: {
          width: 2,
          color: '#999'
        }
      },
      splitLine: {
        length: 10,
        lineStyle: {
          width: 3,
          color: '#999'
        }
      },
      axisLabel: {
        distance: 10,
        color: '#999',
        fontSize: 8
      },
      title: {
        show: true
      },
      detail: {
        height: 40,
        borderRadius: 8,
        offsetCenter: [0, '35%'],
        valueAnimation: true,
        formatter: function (value) {
          return '{value|' + value.toFixed(0) + '}{unit|Dias Lactancia}';
        },
        rich: {
          value: {
            fontSize: 18,
            fontWeight: 'bolder',
            color: '#777'
          },
          unit: {
            fontSize: 20,
            color: '#999',
            padding: [0, 0, -0, 10]
          }
        }
      },
      data: [
        {
          value: <?php echo number_format($averageLactancyDays, 2); ?>
        }
      ]
    }
  ]
};
if (option && typeof option === 'object') {
  myChart.setOption(option);
}
window.addEventListener('resize', myChart.resize);
</script>
<!-- INDICE: Vacas en Ordeño -->
<script>
var dom = document.getElementById('chart-enordeno');
var myChart = echarts.init(dom, null, {
  renderer: 'svg',
  useDirtyRect: false
});
var app = {};
var option;
option = {
  title: {
    text: "Vacas en Ordeño",    
    subtext: "",
    left: "center",
    top:"bottom",    
    textStyle: {
      fontSize: 14,
      color:'#696868',
    },
    subtextStyle: {
      fontSize: 10     
    }
  },
  series: [
    {
      type: 'gauge',
      min: 80,
      max: 90,
      axisLine: {
        lineStyle: {
          width: 20,
          color: [
            [0.2, '#fd666d'],
            [0.4, '#ffa270'],
            [0.6, '#52d165'],
            [0.8, '#358741'],
            [0.9, '#215429'],
            [1, '#215429']
          ]
        }
      },
      pointer: {
        itemStyle: {
          color: 'auto'
        }
      },
      axisTick: {
        distance: -30,
        length: 8,
        lineStyle: {
          color: '#fff',
          width: 2
        }
      },
      splitLine: {
        distance: -30,
        length: 30,
        lineStyle: {
          color: '#fff',
          width: 4
        }
      },
      axisLabel: {
        color: 'inherit',
        distance: 25,
        fontSize: 8
      },
      detail: {
        valueAnimation: true,
        formatter: '{value} %',
        color: 'inherit',
        margin:20,
        padding: 20,
        fontSize:12
      },
      data: [
        {
          value: <?php echo number_format($vacasEnOrdenoPercentage, 2); ?>
        }
      ]
    }
  ]
};
if (option && typeof option === 'object') {
  myChart.setOption(option);
}
window.addEventListener('resize', myChart.resize);
</script>
<!-- INDICE: Incremento Mensual del Peso -->
<script>
var dom = document.getElementById('chart-aumento-mensual-peso');
var myChart = echarts.init(dom, null, {
  renderer: 'svg',
  useDirtyRect: false
});
var app = {};
var option;
option = {
  title: {
    text: "Incremento Mensual Peso",    
    subtext: "",
    left: "center",
    top:"bottom",    
    textStyle: {
      fontSize: 14,
      color:'#696868',
    },
    subtextStyle: {
      fontSize: 10     
    }
  },
  series: [
    {
      type: 'gauge',
      min: 0,
      max: 100,
      axisLine: {
        lineStyle: {
          width: 20,
          color: [
            [0.2, '#fd666d'],
            [0.4, '#ffa270'],
            [0.6, '#52d165'],
            [0.8, '#358741'],
            [0.9, '#215429'],
            [1, '#215429']
          ]
        }
      },
      pointer: {
        itemStyle: {
          color: 'auto'
        }
      },
      axisTick: {
        distance: -30,
        length: 8,
        lineStyle: {
          color: '#fff',
          width: 2
        }
      },
      splitLine: {
        distance: -30,
        length: 30,
        lineStyle: {
          color: '#fff',
          width: 4
        }
      },
      axisLabel: {
        color: 'inherit',
        distance: 25,
        fontSize: 8
      },
      detail: {
        valueAnimation: true,
        formatter: '{value} %',
        color: 'inherit',
        margin:20,
        padding: 20,
        fontSize:12
      },
      data: [
        {
          value: <?php echo number_format($averageMonthlyWeight, 2); ?>
        }
      ]
    }
  ]
};
if (option && typeof option === 'object') {
  myChart.setOption(option);
}
window.addEventListener('resize', myChart.resize);
</script>
<!-- INDICE: Produccion Carnica Vs Costo del Alimento -->
<script>
var dom = document.getElementById('chart-Carne-Alimento');
var myChart = echarts.init(dom, null, {
  renderer: 'svg',
  useDirtyRect: false
});
var app = {};
var option;
option = {
  title: {
    text: "Impacto Concentrado Vs Valor en Pie",    
    subtext: "",
    left: "center",
    top:"bottom",    
    textStyle: {
      fontSize: 14,
      color:'#696868',
    },
    subtextStyle: {
      fontSize: 10     
    }
  },
  series: [
    {
      type: 'gauge',
      min: 0,
      max: 50,
      axisLine: {
        lineStyle: {
          width: 20,
          color: [
            [0.2, '#fd666d'],
            [0.4, '#ffa270'],
            [0.6, '#52d165'],
            [0.8, '#358741'],
            [0.9, '#215429'],
            [1, '#215429']
          ]
        }
      },
      pointer: {
        itemStyle: {
          color: 'auto'
        }
      },
      axisTick: {
        distance: -30,
        length: 8,
        lineStyle: {
          color: '#fff',
          width: 2
        }
      },
      splitLine: {
        distance: -30,
        length: 30,
        lineStyle: {
          color: '#fff',
          width: 4
        }
      },
      axisLabel: {
        color: 'inherit',
        distance: 25,
        fontSize: 8
      },
      detail: {
        valueAnimation: true,
        formatter: '{value} %',
        color: 'inherit',
        margin:20,
        padding: 20,
        fontSize:12
      },
      data: [
        {
          value: <?php echo number_format($concentradoImpact, 2); ?>
        }
      ]
    }
  ]
};
if (option && typeof option === 'object') {
  myChart.setOption(option);
}
window.addEventListener('resize', myChart.resize);
</script>

<!-- Indices de Alimentacion -->
<!-- INDICE: Inicio -->
<script>
var dom = document.getElementById('chart-becerros-concentrado');
var myChart = echarts.init(dom, null, {
  renderer: 'svg',
  useDirtyRect: false
});
var app = {};
var option;
option = {
  title: {
    text: "Inversion en Iniciador",    
    subtext: "",
    left: "center",
    top:"bottom",    
    textStyle: {
      fontSize: 14,
      color:'#696868',
    },
    subtextStyle: {
      fontSize: 10     
    }
  },
  series: [
    {
      type: 'gauge',
      min: 0,
      max: 100,
      axisLine: {
        lineStyle: {
          width: 20,
          color: [
            [0.2, '#fd666d'],
            [0.4, '#ffa270'],
            [0.6, '#7efa48'],
            [0.8, '#9aca8a'],
            [0.9, '#9aca8a'],
            [1, '#9aca8a']
          ]
        }
      },
      pointer: {
        itemStyle: {
          color: 'auto'
        }
      },
      axisTick: {
        distance: -30,
        length: 8,
        lineStyle: {
          color: '#fff',
          width: 2
        }
      },
      splitLine: {
        distance: -30,
        length: 30,
        lineStyle: {
          color: '#fff',
          width: 4
        }
      },
      axisLabel: {
        color: 'inherit',
        distance: 25,
        fontSize: 8
      },
      detail: {
        valueAnimation: true,
        formatter: '{value} %',
        color: 'inherit',
        margin:20,
        padding: 20,
        fontSize:12
      },
      data: [
        {
          value: <?php echo number_format($inicio_percentaje, 2); ?>
        }
      ]
    }
  ]
};
if (option && typeof option === 'object') {
  myChart.setOption(option);
}
window.addEventListener('resize', myChart.resize);
</script>
<!-- INDICE: Crecimiento -->
<script>
var dom = document.getElementById('chart-novillos-concentrado');
var myChart = echarts.init(dom, null, {
  renderer: 'svg',
  useDirtyRect: false
});
var app = {};
var option;
option = {
  title: {
    text: "Inversion en Crecimiento",    
    subtext: "",
    left: "center",
    top:"bottom",    
    textStyle: {
      fontSize: 14,
      color:'#696868',
    },
    subtextStyle: {
      fontSize: 10     
    }
  },
  series: [
    {
      type: 'gauge',
      min: 0,
      max: 100,
      axisLine: {
        lineStyle: {
          width: 20,
          color: [
            [0.2, '#fd666d'],
            [0.4, '#ffa270'],
            [0.6, '#7efa48'],
            [0.8, '#9aca8a'],
            [0.9, '#9aca8a'],
            [1, '#9aca8a']
          ]
        }
      },
      pointer: {
        itemStyle: {
          color: 'auto'
        }
      },
      axisTick: {
        distance: -30,
        length: 8,
        lineStyle: {
          color: '#fff',
          width: 2
        }
      },
      splitLine: {
        distance: -30,
        length: 30,
        lineStyle: {
          color: '#fff',
          width: 4
        }
      },
      axisLabel: {
        color: 'inherit',
        distance: 25,
        fontSize: 8
      },
      detail: {
        valueAnimation: true,
        formatter: '{value} %',
        color: 'inherit',
        margin:20,
        padding: 20,
        fontSize:12
      },
      data: [
        {
          value: <?php echo number_format($crecimiento_percentaje, 2); ?>
        }
      ]
    }
  ]
};
if (option && typeof option === 'object') {
  myChart.setOption(option);
}
window.addEventListener('resize', myChart.resize);
</script>
<!-- INDICE: Finalizador -->
<script>
var dom = document.getElementById('chart-adultos-concentrado');
var myChart = echarts.init(dom, null, {
  renderer: 'svg',
  useDirtyRect: false
});
var app = {};
var option;
option = {
  title: {
    text: "Inversion en Finalizador",    
    subtext: "",
    left: "center",
    top:"bottom",    
    textStyle: {
      fontSize: 14,
      color:'#696868',
    },
    subtextStyle: {
      fontSize: 10     
    }
  },
  series: [
    {
      type: 'gauge',
      min: 0,
      max: 100,
      axisLine: {
        lineStyle: {
          width: 20,
          color: [
            [0.2, '#fd666d'],
            [0.4, '#ffa270'],
            [0.6, '#7efa48'],
            [0.8, '#9aca8a'],
            [0.9, '#9aca8a'],
            [1, '#9aca8a']
          ]
        }
      },
      pointer: {
        itemStyle: {
          color: 'auto'
        }
      },
      axisTick: {
        distance: -30,
        length: 8,
        lineStyle: {
          color: '#fff',
          width: 2
        }
      },
      splitLine: {
        distance: -30,
        length: 30,
        lineStyle: {
          color: '#fff',
          width: 4
        }
      },
      axisLabel: {
        color: 'inherit',
        distance: 25,
        fontSize: 8
      },
      detail: {
        valueAnimation: true,
        formatter: '{value} %',
        color: 'inherit',
        margin:20,
        padding: 20,
        fontSize:12
      },
      data: [
        {
          value: <?php echo number_format($finalizacion_percentaje, 2); ?>
        }
      ]
    }
  ]
};
if (option && typeof option === 'object') {
  myChart.setOption(option);
}
window.addEventListener('resize', myChart.resize);
</script>
<!-- INDICE: Sal x Etapas -->
<!-- Sal Inicio  -->
<script>
var dom = document.getElementById('chart-becerros-sal');
var myChart = echarts.init(dom, null, {
  renderer: 'svg',
  useDirtyRect: false
});
var app = {};
var option;
option = {
  title: {
    text: "Inversion en Iniciador",    
    subtext: "",
    left: "center",
    top:"bottom",    
    textStyle: {
      fontSize: 14,
      color:'#696868',
    },
    subtextStyle: {
      fontSize: 10     
    }
  },
  series: [
    {
      type: 'gauge',
      min: 0,
      max: 100,
      axisLine: {
        lineStyle: {
          width: 20,
          color: [
            [0.2, '#fd666d'],
            [0.4, '#ffa270'],
            [0.6, '#7efa48'],
            [0.8, '#9aca8a'],
            [0.9, '#9aca8a'],
            [1, '#9aca8a']
          ]
        }
      },
      pointer: {
        itemStyle: {
          color: 'auto'
        }
      },
      axisTick: {
        distance: -30,
        length: 8,
        lineStyle: {
          color: '#fff',
          width: 2
        }
      },
      splitLine: {
        distance: -30,
        length: 30,
        lineStyle: {
          color: '#fff',
          width: 4
        }
      },
      axisLabel: {
        color: 'inherit',
        distance: 25,
        fontSize: 8
      },
      detail: {
        valueAnimation: true,
        formatter: '{value} %',
        color: 'inherit',
        margin:20,
        padding: 20,
        fontSize:12
      },
      data: [
        {
          value: <?php echo number_format($inicio_sal_percentaje, 2); ?>
        }
      ]
    }
  ]
};
if (option && typeof option === 'object') {
  myChart.setOption(option);
}
window.addEventListener('resize', myChart.resize);
</script>
<!-- Sal Crecimiento  -->
<script>
var dom = document.getElementById('chart-novillos-sal');
var myChart = echarts.init(dom, null, {
  renderer: 'svg',
  useDirtyRect: false
});
var app = {};
var option;
option = {
  title: {
    text: "Inversion en Crecimiento",    
    subtext: "",
    left: "center",
    top:"bottom",    
    textStyle: {
      fontSize: 14,
      color:'#696868',
    },
    subtextStyle: {
      fontSize: 10     
    }
  },
  series: [
    {
      type: 'gauge',
      min: 0,
      max: 100,
      axisLine: {
        lineStyle: {
          width: 20,
          color: [
            [0.2, '#fd666d'],
            [0.4, '#ffa270'],
            [0.6, '#7efa48'],
            [0.8, '#9aca8a'],
            [0.9, '#9aca8a'],
            [1, '#9aca8a']
          ]
        }
      },
      pointer: {
        itemStyle: {
          color: 'auto'
        }
      },
      axisTick: {
        distance: -30,
        length: 8,
        lineStyle: {
          color: '#fff',
          width: 2
        }
      },
      splitLine: {
        distance: -30,
        length: 30,
        lineStyle: {
          color: '#fff',
          width: 4
        }
      },
      axisLabel: {
        color: 'inherit',
        distance: 25,
        fontSize: 8
      },
      detail: {
        valueAnimation: true,
        formatter: '{value} %',
        color: 'inherit',
        margin:20,
        padding: 20,
        fontSize:12
      },
      data: [
        {
          value: <?php echo number_format($crecimiento_sal_percentaje, 2); ?>
        }
      ]
    }
  ]
};
if (option && typeof option === 'object') {
  myChart.setOption(option);
}
window.addEventListener('resize', myChart.resize);
</script>
<!-- Sal Finalizador  -->
<script>
var dom = document.getElementById('chart-adultos-sal');
var myChart = echarts.init(dom, null, {
  renderer: 'svg',
  useDirtyRect: false
});
var app = {};
var option;
option = {
  title: {
    text: "Inversion en Finalizacion",    
    subtext: "",
    left: "center",
    top:"bottom",    
    textStyle: {
      fontSize: 14,
      color:'#696868',
    },
    subtextStyle: {
      fontSize: 10     
    }
  },
  series: [
    {
      type: 'gauge',
      min: 0,
      max: 100,
      axisLine: {
        lineStyle: {
          width: 20,
          color: [
            [0.2, '#fd666d'],
            [0.4, '#ffa270'],
            [0.6, '#7efa48'],
            [0.8, '#9aca8a'],
            [0.9, '#9aca8a'],
            [1, '#9aca8a']
          ]
        }
      },
      pointer: {
        itemStyle: {
          color: 'auto'
        }
      },
      axisTick: {
        distance: -30,
        length: 8,
        lineStyle: {
          color: '#fff',
          width: 2
        }
      },
      splitLine: {
        distance: -30,
        length: 30,
        lineStyle: {
          color: '#fff',
          width: 4
        }
      },
      axisLabel: {
        color: 'inherit',
        distance: 25,
        fontSize: 8
      },
      detail: {
        valueAnimation: true,
        formatter: '{value} %',
        color: 'inherit',
        margin:20,
        padding: 20,
        fontSize:12
      },
      data: [
        {
          value: <?php echo number_format($finalizacion_sal_percentaje, 2); ?>
        }
      ]
    }
  ]
};
if (option && typeof option === 'object') {
  myChart.setOption(option);
}
window.addEventListener('resize', myChart.resize);
</script>
<!-- INDICE: Melaza x Etapas -->
 <!-- INDICE: Inicio -->
<script>
var dom = document.getElementById('chart-becerros-melaza');
var myChart = echarts.init(dom, null, {
  renderer: 'svg',
  useDirtyRect: false
});
var app = {};
var option;
option = {
  title: {
    text: "Inversion Inicio",    
    subtext: "",
    left: "center",
    top:"bottom",    
    textStyle: {
      fontSize: 14,
      color:'#696868',
    },
    subtextStyle: {
      fontSize: 10     
    }
  },
  series: [
    {
      type: 'gauge',
      min: 0,
      max: 100,
      axisLine: {
        lineStyle: {
          width: 20,
          color: [
            [0.2, '#fd666d'],
            [0.4, '#ffa270'],
            [0.6, '#7efa48'],
            [0.8, '#9aca8a'],
            [0.9, '#9aca8a'],
            [1, '#9aca8a']
          ]
        }
      },
      pointer: {
        itemStyle: {
          color: 'auto'
        }
      },
      axisTick: {
        distance: -30,
        length: 8,
        lineStyle: {
          color: '#fff',
          width: 2
        }
      },
      splitLine: {
        distance: -30,
        length: 30,
        lineStyle: {
          color: '#fff',
          width: 4
        }
      },
      axisLabel: {
        color: 'inherit',
        distance: 25,
        fontSize: 8
      },
      detail: {
        valueAnimation: true,
        formatter: '{value} %',
        color: 'inherit',
        margin:20,
        padding: 20,
        fontSize:12
      },
      data: [
        {
          value: <?php echo number_format($inicio_melaza_percentaje, 2); ?>
        }
      ]
    }
  ]
};
if (option && typeof option === 'object') {
  myChart.setOption(option);
}
window.addEventListener('resize', myChart.resize);
</script>
<!-- INDICE: Crecimiento -->
<script>
var dom = document.getElementById('chart-novillos-melaza');
var myChart = echarts.init(dom, null, {
  renderer: 'svg',
  useDirtyRect: false
});
var app = {};
var option;
option = {
  title: {
    text: "Inversion Crecmiento",    
    subtext: "",
    left: "center",
    top:"bottom",    
    textStyle: {
      fontSize: 14,
      color:'#696868',
    },
    subtextStyle: {
      fontSize: 10     
    }
  },
  series: [
    {
      type: 'gauge',
      min: 0,
      max: 100,
      axisLine: {
        lineStyle: {
          width: 20,
          color: [
            [0.2, '#fd666d'],
            [0.4, '#ffa270'],
            [0.6, '#7efa48'],
            [0.8, '#9aca8a'],
            [0.9, '#9aca8a'],
            [1, '#9aca8a']
          ]
        }
      },
      pointer: {
        itemStyle: {
          color: 'auto'
        }
      },
      axisTick: {
        distance: -30,
        length: 8,
        lineStyle: {
          color: '#fff',
          width: 2
        }
      },
      splitLine: {
        distance: -30,
        length: 30,
        lineStyle: {
          color: '#fff',
          width: 4
        }
      },
      axisLabel: {
        color: 'inherit',
        distance: 25,
        fontSize: 8
      },
      detail: {
        valueAnimation: true,
        formatter: '{value} %',
        color: 'inherit',
        margin:20,
        padding: 20,
        fontSize:12
      },
      data: [
        {
          value: <?php echo number_format($crecimiento_melaza_percentaje, 2); ?>
        }
      ]
    }
  ]
};
if (option && typeof option === 'object') {
  myChart.setOption(option);
}
window.addEventListener('resize', myChart.resize);
</script>
<!-- INDICE: Finalizador -->
<script>
var dom = document.getElementById('chart-adultos-melaza');
var myChart = echarts.init(dom, null, {
  renderer: 'svg',
  useDirtyRect: false
});
var app = {};
var option;
option = {
  title: {
    text: "Inversion Finalizacion",    
    subtext: "",
    left: "center",
    top:"bottom",    
    textStyle: {
      fontSize: 14,
      color:'#696868',
    },
    subtextStyle: {
      fontSize: 10     
    }
  },
  series: [
    {
      type: 'gauge',
      min: 0,
      max: 100,
      axisLine: {
        lineStyle: {
          width: 20,
          color: [
            [0.2, '#fd666d'],
            [0.4, '#ffa270'],
            [0.6, '#7efa48'],
            [0.8, '#9aca8a'],
            [0.9, '#9aca8a'],
            [1, '#9aca8a']
          ]
        }
      },
      pointer: {
        itemStyle: {
          color: 'auto'
        }
      },
      axisTick: {
        distance: -30,
        length: 8,
        lineStyle: {
          color: '#fff',
          width: 2
        }
      },
      splitLine: {
        distance: -30,
        length: 30,
        lineStyle: {
          color: '#fff',
          width: 4
        }
      },
      axisLabel: {
        color: 'inherit',
        distance: 25,
        fontSize: 8
      },
      detail: {
        valueAnimation: true,
        formatter: '{value} %',
        color: 'inherit',
        margin:20,
        padding: 20,
        fontSize:12
      },
      data: [
        {
          value: <?php echo number_format($finalizacion_melaza_percentaje, 2); ?>
        }
      ]
    }
  ]
};
if (option && typeof option === 'object') {
  myChart.setOption(option);
}
window.addEventListener('resize', myChart.resize);
</script>
<!-- Indices de Salud -->
<!-- INDICE: Vacas en Ordeño -->
<!-- Fiebre Aftosa -->
<script>
var dom = document.getElementById('chart-aftosa');
var myChart = echarts.init(dom, null, {
  renderer: 'svg',
  useDirtyRect: false
});
var app = {};
var option;
option = {
  title: {
    text: "% Vacunados Aftosa",    
    subtext: "",
    left: "center",
    top:"bottom",    
    textStyle: {
      fontSize: 14,
      color:'#696868',
    },
    subtextStyle: {
      fontSize: 10     
    }
  },
  series: [
    {
      type: 'gauge',
      min: 80,
      max: 90,
      axisLine: {
        lineStyle: {
          width: 20,
          color: [
            [0.2, '#fd666d'],
            [0.4, '#ffa270'],
            [0.6, '#c9ffb8'],
            [0.8, '#9aca8a'],
            [0.9, '#9aca8a'],
            [1, '#9aca8a']
          ]
        }
      },
      pointer: {
        itemStyle: {
          color: 'auto'
        }
      },
      axisTick: {
        distance: -30,
        length: 8,
        lineStyle: {
          color: '#fff',
          width: 2
        }
      },
      splitLine: {
        distance: -30,
        length: 30,
        lineStyle: {
          color: '#fff',
          width: 4
        }
      },
      axisLabel: {
        color: 'inherit',
        distance: 25,
        fontSize: 8
      },
      detail: {
        valueAnimation: true,
        formatter: '{value} %',
        color: 'inherit',
        margin:20,
        padding: 20,
        fontSize:12
      },
      data: [
        {
          value: <?php echo number_format($aftosa_vaccinated_percentage, 2); ?>
        }
      ]
    }
  ]
};
if (option && typeof option === 'object') {
  myChart.setOption(option);
}
window.addEventListener('resize', myChart.resize);
</script>
<!-- Brucelosis -->
<script>
var dom = document.getElementById('chart-brucelosis');
var myChart = echarts.init(dom, null, {
  renderer: 'svg',
  useDirtyRect: false
});
var app = {};
var option;
option = {
  title: {
    text: "% Vacunados Brucelosis",    
    subtext: "",
    left: "center",
    top:"bottom",    
    textStyle: {
      fontSize: 14,
      color:'#696868',
    },
    subtextStyle: {
      fontSize: 10     
    }
  },
  series: [
    {
      type: 'gauge',
      min: 80,
      max: 90,
      axisLine: {
        lineStyle: {
          width: 20,
          color: [
            [0.2, '#fd666d'],
            [0.4, '#ffa270'],
            [0.6, '#c9ffb8'],
            [0.8, '#9aca8a'],
            [0.9, '#9aca8a'],
            [1, '#9aca8a']
          ]
        }
      },
      pointer: {
        itemStyle: {
          color: 'auto'
        }
      },
      axisTick: {
        distance: -30,
        length: 8,
        lineStyle: {
          color: '#fff',
          width: 2
        }
      },
      splitLine: {
        distance: -30,
        length: 30,
        lineStyle: {
          color: '#fff',
          width: 4
        }
      },
      axisLabel: {
        color: 'inherit',
        distance: 25,
        fontSize: 8
      },
      detail: {
        valueAnimation: true,
        formatter: '{value} %',
        color: 'inherit',
        margin:20,
        padding: 20,
        fontSize:12
      },
      data: [
        {
          value: <?php echo number_format($brucelosis_vaccinated_percentage, 2); ?>
        }
      ]
    }
  ]
};
if (option && typeof option === 'object') {
  myChart.setOption(option);
}
window.addEventListener('resize', myChart.resize);
</script>
<!-- IBR -->
<script>
var dom = document.getElementById('chart-ibr');
var myChart = echarts.init(dom, null, {
  renderer: 'svg',
  useDirtyRect: false
});
var app = {};
var option;
option = {
  title: {
    text: "% Vacunados IBR",    
    subtext: "",
    left: "center",
    top:"bottom",    
    textStyle: {
      fontSize: 14,
      color:'#696868',
    },
    subtextStyle: {
      fontSize: 10     
    }
  },
  series: [
    {
      type: 'gauge',
      min: 80,
      max: 90,
      axisLine: {
        lineStyle: {
          width: 20,
          color: [
            [0.2, '#fd666d'],
            [0.4, '#ffa270'],
            [0.6, '#c9ffb8'],
            [0.8, '#9aca8a'],
            [0.9, '#9aca8a'],
            [1, '#9aca8a']
          ]
        }
      },
      pointer: {
        itemStyle: {
          color: 'auto'
        }
      },
      axisTick: {
        distance: -30,
        length: 8,
        lineStyle: {
          color: '#fff',
          width: 2
        }
      },
      splitLine: {
        distance: -30,
        length: 30,
        lineStyle: {
          color: '#fff',
          width: 4
        }
      },
      axisLabel: {
        color: 'inherit',
        distance: 25,
        fontSize: 8
      },
      detail: {
        valueAnimation: true,
        formatter: '{value} %',
        color: 'inherit',
        margin:20,
        padding: 20,
        fontSize:12
      },
      data: [
        {
          value: <?php echo number_format($ibr_vaccinated_percentage, 2); ?>
        }
      ]
    }
  ]
};
if (option && typeof option === 'object') {
  myChart.setOption(option);
}
window.addEventListener('resize', myChart.resize);
</script>
<!-- Carbunco -->
<script>
var dom = document.getElementById('chart-carbunco');
var myChart = echarts.init(dom, null, {
  renderer: 'svg',
  useDirtyRect: false
});
var app = {};
var option;
option = {
  title: {
    text: "% Vacunados Carbunco",    
    subtext: "",
    left: "center",
    top:"bottom",    
    textStyle: {
      fontSize: 14,
      color:'#696868',
    },
    subtextStyle: {
      fontSize: 10     
    }
  },
  series: [
    {
      type: 'gauge',
      min: 80,
      max: 90,
      axisLine: {
        lineStyle: {
          width: 20,
          color: [
            [0.2, '#fd666d'],
            [0.4, '#ffa270'],
            [0.6, '#c9ffb8'],
            [0.8, '#9aca8a'],
            [0.9, '#9aca8a'],
            [1, '#9aca8a']
          ]
        }
      },
      pointer: {
        itemStyle: {
          color: 'auto'
        }
      },
      axisTick: {
        distance: -30,
        length: 8,
        lineStyle: {
          color: '#fff',
          width: 2
        }
      },
      splitLine: {
        distance: -30,
        length: 30,
        lineStyle: {
          color: '#fff',
          width: 4
        }
      },
      axisLabel: {
        color: 'inherit',
        distance: 25,
        fontSize: 8
      },
      detail: {
        valueAnimation: true,
        formatter: '{value} %',
        color: 'inherit',
        margin:20,
        padding: 20,
        fontSize:12
      },
      data: [
        {
          value: <?php echo number_format($carbunco_vaccinated_percentage, 2); ?>
        }
      ]
    }
  ]
};
if (option && typeof option === 'object') {
  myChart.setOption(option);
}
window.addEventListener('resize', myChart.resize);
</script>
<!-- CBR -->
<script>
var dom = document.getElementById('chart-cbr');
var myChart = echarts.init(dom, null, {
  renderer: 'svg',
  useDirtyRect: false
});
var app = {};
var option;
option = {
  title: {
    text: "% Vacunados CBR",    
    subtext: "",
    left: "center",
    top:"bottom",    
    textStyle: {
      fontSize: 14,
      color:'#696868',
    },
    subtextStyle: {
      fontSize: 10     
    }
  },
  series: [
    {
      type: 'gauge',
      min: 80,
      max: 90,
      axisLine: {
        lineStyle: {
          width: 20,
          color: [
            [0.2, '#fd666d'],
            [0.4, '#ffa270'],
            [0.6, '#c9ffb8'],
            [0.8, '#9aca8a'],
            [0.9, '#9aca8a'],
            [1, '#9aca8a']
          ]
        }
      },
      pointer: {
        itemStyle: {
          color: 'auto'
        }
      },
      axisTick: {
        distance: -30,
        length: 8,
        lineStyle: {
          color: '#fff',
          width: 2
        }
      },
      splitLine: {
        distance: -30,
        length: 30,
        lineStyle: {
          color: '#fff',
          width: 4
        }
      },
      axisLabel: {
        color: 'inherit',
        distance: 25,
        fontSize: 8
      },
      detail: {
        valueAnimation: true,
        formatter: '{value} %',
        color: 'inherit',
        margin:20,
        padding: 20,
        fontSize:12
      },
      data: [
        {
          value: <?php echo number_format($cbr_vaccinated_percentage, 2); ?>
        }
      ]
    }
  ]
};
if (option && typeof option === 'object') {
  myChart.setOption(option);
}
window.addEventListener('resize', myChart.resize);
</script>
<!-- Garrapatas -->
<script>
var dom = document.getElementById('chart-garrapatas');
var myChart = echarts.init(dom, null, {
  renderer: 'svg',
  useDirtyRect: false
});
var app = {};
var option;
option = {
  title: {
    text: "% Bañados Garrapatas",    
    subtext: "",
    left: "center",
    top:"bottom",    
    textStyle: {
      fontSize: 14,
      color:'#696868',
    },
    subtextStyle: {
      fontSize: 10     
    }
  },
  series: [
    {
      type: 'gauge',
      min: 80,
      max: 90,
      axisLine: {
        lineStyle: {
          width: 20,
          color: [
            [0.2, '#fd666d'],
            [0.4, '#ffa270'],
            [0.6, '#c9ffb8'],
            [0.8, '#9aca8a'],
            [0.9, '#9aca8a'],
            [1, '#9aca8a']
          ]
        }
      },
      pointer: {
        itemStyle: {
          color: 'auto'
        }
      },
      axisTick: {
        distance: -30,
        length: 8,
        lineStyle: {
          color: '#fff',
          width: 2
        }
      },
      splitLine: {
        distance: -30,
        length: 30,
        lineStyle: {
          color: '#fff',
          width: 4
        }
      },
      axisLabel: {
        color: 'inherit',
        distance: 25,
        fontSize: 8
      },
      detail: {
        valueAnimation: true,
        formatter: '{value} %',
        color: 'inherit',
        margin:20,
        padding: 20,
        fontSize:12
      },
      data: [
        {
          value: <?php echo number_format($garrapatas_vaccinated_percentage, 2); ?>
        }
      ]
    }
  ]
};
if (option && typeof option === 'object') {
  myChart.setOption(option);
}
window.addEventListener('resize', myChart.resize);
</script>
<!-- Parasitos -->
<script>
var dom = document.getElementById('chart-parasitos');
var myChart = echarts.init(dom, null, {
  renderer: 'svg',
  useDirtyRect: false
});
var app = {};
var option;
option = {
  title: {
    text: "% Desparasitados",    
    subtext: "",
    left: "center",
    top:"bottom",    
    textStyle: {
      fontSize: 14,
      color:'#696868',
    },
    subtextStyle: {
      fontSize: 10     
    }
  },
  series: [
    {
      type: 'gauge',
      min: 80,
      max: 90,
      axisLine: {
        lineStyle: {
          width: 20,
          color: [
            [0.2, '#fd666d'],
            [0.4, '#ffa270'],
            [0.6, '#c9ffb8'],
            [0.8, '#9aca8a'],
            [0.9, '#9aca8a'],
            [1, '#9aca8a']
          ]
        }
      },
      pointer: {
        itemStyle: {
          color: 'auto'
        }
      },
      axisTick: {
        distance: -30,
        length: 8,
        lineStyle: {
          color: '#fff',
          width: 2
        }
      },
      splitLine: {
        distance: -30,
        length: 30,
        lineStyle: {
          color: '#fff',
          width: 4
        }
      },
      axisLabel: {
        color: 'inherit',
        distance: 25,
        fontSize: 8
      },
      detail: {
        valueAnimation: true,
        formatter: '{value} %',
        color: 'inherit',
        margin:20,
        padding: 20,
        fontSize:12
      },
      data: [
        {
          value: <?php echo number_format($parasitos_vaccinated_percentage, 2); ?>
        }
      ]
    }
  ]
};
if (option && typeof option === 'object') {
  myChart.setOption(option);
}
window.addEventListener('resize', myChart.resize);
</script>
<!-- Mastitis -->
<script>
var dom = document.getElementById('chart-mastitis');
var myChart = echarts.init(dom, null, {
  renderer: 'svg',
  useDirtyRect: false
});
var app = {};
var option;
option = {
  title: {
    text: "% Tratados Mastitis",    
    subtext: "",
    left: "center",
    top:"bottom",    
    textStyle: {
      fontSize: 14,
      color:'#696868',
    },
    subtextStyle: {
      fontSize: 10     
    }
  },
  series: [
    {
      type: 'gauge',
      min: 80,
      max: 90,
      axisLine: {
        lineStyle: {
          width: 20,
          color: [
            [0.2, '#fd666d'],
            [0.4, '#ffa270'],
            [0.6, '#c9ffb8'],
            [0.8, '#9aca8a'],
            [0.9, '#9aca8a'],
            [1, '#9aca8a']
          ]
        }
      },
      pointer: {
        itemStyle: {
          color: 'auto'
        }
      },
      axisTick: {
        distance: -30,
        length: 8,
        lineStyle: {
          color: '#fff',
          width: 2
        }
      },
      splitLine: {
        distance: -30,
        length: 30,
        lineStyle: {
          color: '#fff',
          width: 4
        }
      },
      axisLabel: {
        color: 'inherit',
        distance: 25,
        fontSize: 8
      },
      detail: {
        valueAnimation: true,
        formatter: '{value} %',
        color: 'inherit',
        margin:20,
        padding: 20,
        fontSize:12
      },
      data: [
        {
          value: <?php echo number_format($mastitis_vaccinated_percentage, 2); ?>
        }
      ]
    }
  ]
};
if (option && typeof option === 'object') {
  myChart.setOption(option);
}
window.addEventListener('resize', myChart.resize);
</script>
<!-- Indices de Reproduccion -->
<!-- INDICE: Preñez 1er Celo -->
<script>
var dom = document.getElementById('chart-1er-celo');
var myChart = echarts.init(dom, null, {
  renderer: 'svg',
  useDirtyRect: false
});
var app = {};
var option;
option = {
  title: {
    text: "Preñez 1er Celo",    
    subtext: "",
    left: "center",
    top:"bottom",    
    textStyle: {
      fontSize: 14,
      color:'#696868',
    },
    subtextStyle: {
      fontSize: 10     
    }
  },
  series: [
    {
      type: 'gauge',
      min: 0,
      max: 100,
      axisLine: {
        lineStyle: {
          width: 20,
          color: [
            [0.2, '#fd666d'],
            [0.4, '#ffa270'],
            [0.6, '#7efa48'],
            [0.8, '#9aca8a'],
            [0.9, '#9aca8a'],
            [1, '#9aca8a']
          ]
        }
      },
      pointer: {
        itemStyle: {
          color: 'auto'
        }
      },
      axisTick: {
        distance: -30,
        length: 8,
        lineStyle: {
          color: '#fff',
          width: 2
        }
      },
      splitLine: {
        distance: -30,
        length: 30,
        lineStyle: {
          color: '#fff',
          width: 4
        }
      },
      axisLabel: {
        color: 'inherit',
        distance: 25,
        fontSize: 8
      },
      detail: {
        valueAnimation: true,
        formatter: '{value} %',
        color: 'inherit',
        margin:20,
        padding: 20,
        fontSize:12
      },
      data: [
        {
          value: <?php echo number_format($porcentaje_1era_inseminacion, 2); ?>
        }
      ]
    }
  ]
};
if (option && typeof option === 'object') {
  myChart.setOption(option);
}
window.addEventListener('resize', myChart.resize);
</script>
<!-- INDICE: Preñez 2do Celo -->
<script>
var dom = document.getElementById('chart-2do-celo');
var myChart = echarts.init(dom, null, {
  renderer: 'svg',
  useDirtyRect: false
});
var app = {};
var option;
option = {
  title: {
    text: "Preñez 2do Celo",    
    subtext: "",
    left: "center",
    top:"bottom",    
    textStyle: {
      fontSize: 14,
      color:'#696868',
    },
    subtextStyle: {
      fontSize: 10     
    }
  },
  series: [
    {
      type: 'gauge',
      min: 0,
      max: 100,
      axisLine: {
        lineStyle: {
          width: 20,
          color: [
            [0.2, '#fd666d'],
            [0.4, '#ffa270'],
            [0.6, '#7efa48'],
            [0.8, '#9aca8a'],
            [0.9, '#9aca8a'],
            [1, '#9aca8a']
          ]
        }
      },
      pointer: {
        itemStyle: {
          color: 'auto'
        }
      },
      axisTick: {
        distance: -30,
        length: 8,
        lineStyle: {
          color: '#fff',
          width: 2
        }
      },
      splitLine: {
        distance: -30,
        length: 30,
        lineStyle: {
          color: '#fff',
          width: 4
        }
      },
      axisLabel: {
        color: 'inherit',
        distance: 25,
        fontSize: 8
      },
      detail: {
        valueAnimation: true,
        formatter: '{value} %',
        color: 'inherit',
        margin:20,
        padding: 20,
        fontSize:12
      },
      data: [
        {
          value: <?php echo number_format($porcentaje_2da_inseminacion, 2); ?>
        }
      ]
    }
  ]
};
if (option && typeof option === 'object') {
  myChart.setOption(option);
}
window.addEventListener('resize', myChart.resize);
</script>
<!-- INDICE: Preñez 3er Celo -->
<script>
var dom = document.getElementById('chart-3er-celo');
var myChart = echarts.init(dom, null, {
  renderer: 'svg',
  useDirtyRect: false
});
var app = {};
var option;
option = {
  title: {
    text: "Preñez 3er Celo",    
    subtext: "",
    left: "center",
    top:"bottom",    
    textStyle: {
      fontSize: 14,
      color:'#696868',
    },
    subtextStyle: {
      fontSize: 10     
    }
  },
  series: [
    {
      type: 'gauge',
      min: 0,
      max: 100,
      axisLine: {
        lineStyle: {
          width: 20,
          color: [
            [0.2, '#fd666d'],
            [0.4, '#ffa270'],
            [0.6, '#7efa48'],
            [0.8, '#9aca8a'],
            [0.9, '#9aca8a'],
            [1, '#9aca8a']
          ]
        }
      },
      pointer: {
        itemStyle: {
          color: 'auto'
        }
      },
      axisTick: {
        distance: -30,
        length: 8,
        lineStyle: {
          color: '#fff',
          width: 2
        }
      },
      splitLine: {
        distance: -30,
        length: 30,
        lineStyle: {
          color: '#fff',
          width: 4
        }
      },
      axisLabel: {
        color: 'inherit',
        distance: 25,
        fontSize: 8
      },
      detail: {
        valueAnimation: true,
        formatter: '{value} %',
        color: 'inherit',
        margin:20,
        padding: 20,
        fontSize:12
      },
      data: [
        {
          value: <?php echo number_format($porcentaje_3era_inseminacion, 2); ?>
        }
      ]
    }
  ]
};
if (option && typeof option === 'object') {
  myChart.setOption(option);
}
window.addEventListener('resize', myChart.resize);
</script>

<!-- INDICE: Dias entre Partos -->
<script>
var dom = document.getElementById('chart-dias-gestacion');
var myChart = echarts.init(dom, null, {
  renderer: 'canvas',
  useDirtyRect: false
});
var app = {};
var option;
option = {
  series: [
    {
      type: 'gauge',
      startAngle: 180,
      endAngle: 0,
      min: 200,
      max: 300,
      splitNumber: 8,
      itemStyle: {
        color: '#9aca8a',
        shadowColor: 'rgba(0,138,255,0.45)',
        shadowBlur: 10,
        shadowOffsetX: 2,
        shadowOffsetY: 2
      },
      progress: {
        show: true,
        roundCap: true,
        width: 10
      },
      pointer: {
        icon: 'path://M2090.36389,615.30999 L2090.36389,615.30999 C2091.48372,615.30999 2092.40383,616.194028 2092.44859,617.312956 L2096.90698,728.755929 C2097.05155,732.369577 2094.2393,735.416212 2090.62566,735.56078 C2090.53845,735.564269 2090.45117,735.566014 2090.36389,735.566014 L2090.36389,735.566014 C2086.74736,735.566014 2083.81557,732.63423 2083.81557,729.017692 C2083.81557,728.930412 2083.81732,728.84314 2083.82081,728.755929 L2088.2792,617.312956 C2088.32396,616.194028 2089.24407,615.30999 2090.36389,615.30999 Z',
        length: '75%',
        width: 10,
        offsetCenter: [0, '5%']
      },
      axisLine: {
        roundCap: true,
        lineStyle: {
          width: 2
        }
      },
      axisTick: {
        splitNumber: 2,
        lineStyle: {
          width: 2,
          color: '#999'
        }
      },
      splitLine: {
        length: 10,
        lineStyle: {
          width: 3,
          color: '#999'
        }
      },
      axisLabel: {
        distance: 10,
        color: '#999',
        fontSize: 8
      },
      title: {
        show: true
      },
      detail: {
        width: '100%',
        lineHeight: 40,
        height: 40,
        borderRadius: 8,
        offsetCenter: [0, '35%'],
        valueAnimation: true,
        formatter: function (value) {
          return '{value|' + value.toFixed(0) + '}{unit|Dias de Gestacion}';
        },
        rich: {
          value: {
            fontSize: 18,
            fontWeight: 'bolder',
            color: '#777'
          },
          unit: {
            fontSize: 20,
            color: '#999',
            padding: [0, 0, -0, 10]
          }
        }
      },
      data: [
        {
          value: <?php echo number_format($averageDays, 0); ?>
        }
      ]
    }
  ]
};
if (option && typeof option === 'object') {
  myChart.setOption(option);
}
window.addEventListener('resize', myChart.resize);
</script>

<!-- INDICE: Dias Entre Partos -->
<script>
var dom = document.getElementById('chart-dias-entre-partos');
var myChart = echarts.init(dom, null, {
  renderer: 'canvas',
  useDirtyRect: false
});
var app = {};
var option;
option = {
  series: [
    {
      type: 'gauge',
      startAngle: 180,
      endAngle: 0,
      min: 300,
      max: 400,
      splitNumber: 8,
      itemStyle: {
        color: '#9aca8a',
        shadowColor: 'rgba(0,138,255,0.45)',
        shadowBlur: 10,
        shadowOffsetX: 2,
        shadowOffsetY: 2
      },
      progress: {
        show: true,
        roundCap: true,
        width: 10
      },
      pointer: {
        icon: 'path://M2090.36389,615.30999 L2090.36389,615.30999 C2091.48372,615.30999 2092.40383,616.194028 2092.44859,617.312956 L2096.90698,728.755929 C2097.05155,732.369577 2094.2393,735.416212 2090.62566,735.56078 C2090.53845,735.564269 2090.45117,735.566014 2090.36389,735.566014 L2090.36389,735.566014 C2086.74736,735.566014 2083.81557,732.63423 2083.81557,729.017692 C2083.81557,728.930412 2083.81732,728.84314 2083.82081,728.755929 L2088.2792,617.312956 C2088.32396,616.194028 2089.24407,615.30999 2090.36389,615.30999 Z',
        length: '75%',
        width: 10,
        offsetCenter: [0, '5%']
      },
      axisLine: {
        roundCap: true,
        lineStyle: {
          width: 2
        }
      },
      axisTick: {
        splitNumber: 2,
        lineStyle: {
          width: 2,
          color: '#999'
        }
      },
      splitLine: {
        length: 10,
        lineStyle: {
          width: 3,
          color: '#999'
        }
      },
      axisLabel: {
        distance: 10,
        color: '#999',
        fontSize: 8
      },
      title: {
        show: true
      },
      detail: {
        width: '100%',
        lineHeight: 40,
        height: 40,
        borderRadius: 8,
        offsetCenter: [0, '35%'],
        valueAnimation: true,
        formatter: function (value) {
          return '{value|' + value.toFixed(0) + '}{unit|Dias Entre Partos}';
        },
        rich: {
          value: {
            fontSize: 18,
            fontWeight: 'bolder',
            color: '#777'
          },
          unit: {
            fontSize: 20,
            color: '#999',
            padding: [0, 0, -0, 10]
          }
        }
      },
      data: [
        {
          value: <?php echo number_format($averageDaysDifference, 0); ?>
        }
      ]
    }
  ]
};
if (option && typeof option === 'object') {
  myChart.setOption(option);
}
window.addEventListener('resize', myChart.resize);
</script>
<!-- INDICE: Partos anuales -->
<script>
var dom = document.getElementById('chart-partos-anuales');
var myChart = echarts.init(dom, null, {
  renderer: 'canvas',
  useDirtyRect: false
});
var app = {};
var option;
option = {
  series: [
    {
      type: 'gauge',
      startAngle: 180,
      endAngle: 0,
      min: 0.5,
      max: 1.5,
      splitNumber: 8,
      itemStyle: {
        color: '#9aca8a',
        shadowColor: 'rgba(0,138,255,0.45)',
        shadowBlur: 10,
        shadowOffsetX: 2,
        shadowOffsetY: 2
      },
      progress: {
        show: true,
        roundCap: true,
        width: 10
      },
      pointer: {
        icon: 'path://M2090.36389,615.30999 L2090.36389,615.30999 C2091.48372,615.30999 2092.40383,616.194028 2092.44859,617.312956 L2096.90698,728.755929 C2097.05155,732.369577 2094.2393,735.416212 2090.62566,735.56078 C2090.53845,735.564269 2090.45117,735.566014 2090.36389,735.566014 L2090.36389,735.566014 C2086.74736,735.566014 2083.81557,732.63423 2083.81557,729.017692 C2083.81557,728.930412 2083.81732,728.84314 2083.82081,728.755929 L2088.2792,617.312956 C2088.32396,616.194028 2089.24407,615.30999 2090.36389,615.30999 Z',
        length: '75%',
        width: 10,
        offsetCenter: [0, '5%']
      },
      axisLine: {
        roundCap: true,
        lineStyle: {
          width: 2
        }
      },
      axisTick: {
        splitNumber: 2,
        lineStyle: {
          width: 2,
          color: '#999'
        }
      },
      splitLine: {
        length: 10,
        lineStyle: {
          width: 3,
          color: '#999'
        }
      },
      axisLabel: {
        distance: 10,
        color: '#999',
        fontSize: 8
      },
      title: {
        show: true
      },
      detail: {
        width: '100%',
        lineHeight: 40,
        height: 40,
        borderRadius: 8,
        offsetCenter: [0, '35%'],
        valueAnimation: true,
        formatter: function (value) {
          return '{value|' + value.toFixed(2) + '}{unit|Partos por Año}';
        },
        rich: {
          value: {
            fontSize: 18,
            fontWeight: 'bolder',
            color: '#777'
          },
          unit: {
            fontSize: 20,
            color: '#999',
            padding: [0, 0, -0, 10]
          }
        }
      },
      data: [
        {
          value: <?php echo number_format(365/$averageDaysDifference, 2); ?>
        }
      ]
    }
  ]
};
if (option && typeof option === 'object') {
  myChart.setOption(option);
}
window.addEventListener('resize', myChart.resize);
</script>
<!-- Indices Otros -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

</body>
</html>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get all buttons in the scroll-icons-container
    const scrollButtons = document.querySelectorAll('.scroll-icons-container .btn');
    
    scrollButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Prevent default collapse behavior
            e.preventDefault();
            
            // Get the target section ID from data-bs-target
            const targetId = this.getAttribute('data-bs-target').replace('#', '');
            const targetSection = document.getElementById(targetId);
            
            if (targetSection) {
                // Add a small delay to allow for the collapse animation
                setTimeout(() => {
                    // Scroll to the section with smooth behavior
                    targetSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start',
                        inline: 'nearest'
                    });
                    
                    // Show the collapsed section
                    const bsCollapse = new bootstrap.Collapse(targetSection, {
                        show: true
                    });
                }, 100);
            }
        });
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const backToTopButton = document.getElementById('backToTop');
    
    // Show/hide button based on scroll position
    function toggleBackToTopButton() {
        if (window.scrollY > 300) {
            backToTopButton.classList.add('visible');
        } else {
            backToTopButton.classList.remove('visible');
        }
    }
    
    // Smooth scroll to top
    function scrollToTop(e) {
        e.preventDefault();
        
        // First, check if native smooth scrolling is supported
        if ('scrollBehavior' in document.documentElement.style) {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        } else {
            // Fallback for browsers that don't support smooth scrolling
            const scrollStep = -window.scrollY / (500 / 15); // 500ms duration
            
            function scrollAnimation() {
                if (window.scrollY !== 0) {
                    window.scrollBy(0, scrollStep);
                    requestAnimationFrame(scrollAnimation);
                }
            }
            
            requestAnimationFrame(scrollAnimation);
        }
    }
    
    // Add scroll event listener with throttling
    let isScrolling = false;
    
    window.addEventListener('scroll', function() {
        if (!isScrolling) {
            window.requestAnimationFrame(function() {
                toggleBackToTopButton();
                isScrolling = false;
            });
            isScrolling = true;
        }
    });
    
    // Add click event listener
    backToTopButton.addEventListener('click', scrollToTop);
    
    // Optional: Hide button on page load
    toggleBackToTopButton();
    
    // Optional: Add keyboard support
    backToTopButton.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            scrollToTop(e);
        }
    });
});
</script>

<!-- Back to Top Button -->
<button id="backToTop" class="btn btn-success back-to-top" title="Volver arriba">
    <i class="fas fa-arrow-up"></i>
</button>