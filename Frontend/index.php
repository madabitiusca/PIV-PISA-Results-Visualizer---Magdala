<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pisa Data Viewer</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="header">
    <h1>Pisa Data Viewer</h1>
    <p>Explore OECD PISA scores and World Bank Education Statistics</p>
</header>
<main class="container">
        <section class="filters">
        <label for="country">
            Țară
            <select id="country">
                <option value="">Loading...</option>
            </select>
        </label>

        <label for="discipline">
            Disciplină
            <select id="discipline">
                <option value="math">Mathematics</option>
                <option value="reading">Reading</option>
                <option value="science">Science</option>
            </select>
        </label>

        <label for="subject">
            Categorie
            <select id="subject">
                <option value="total">Total</option>
                <option value="boys">Boys</option>
                <option value="girls">Girls</option>
            </select>
        </label>
        <label for="year">
            An
            <select id="year">
                <option value="">Toți anii</option>
                <option value="2018">2018</option>
                <option value="2015">2015</option>
                <option value="2012">2012</option>
                <option value="2009">2009</option>
                <option value="2006">2006</option>
                <option value="2003">2003</option>
                <option value="2000">2000</option>
            </select>
        </label>

        <button id="searchBtn" type="button">Search</button>
    </section>
</main>

</body>
</html>