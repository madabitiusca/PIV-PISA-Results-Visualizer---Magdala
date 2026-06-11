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
                <option value="">Loading</option>
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

       <section class="cards">
        <article class="card">
            <h2>Rezultate</h2>
            <p id="resultCount">0</p>
        </article>

        <article class="card">
            <h2>Scor mediu</h2>
            <p id="avgScore">-</p>
        </article>

        <article class="card">
            <h2>Țară selectată</h2>
            <p id="selectedCountry">-</p>
        </article>
    </section>

    <section class="chart-card">
        <h2>Evoluție scor PISA</h2>
        <svg id="lineChart" viewBox="0 0 800 360" role="img" aria-label="Grafic evoluție scor PISA"></svg>
    </section>

    <section class="chart-card">
        <h2>Comparație țări</h2>
        <svg id="barChart" viewBox="0 0 800 360" role="img" aria-label="Grafic comparație țări"></svg>
    </section>

    <section class="chart-card">
        <h2>Math vs Reading vs Science</h2>
        <svg id="radarChart" viewBox="0 0 800 360" role="img" aria-label="Grafic radar discipline PISA"></svg>
    </section>

    <section class="table-card">
        <h2>Rezultate PISA</h2>

        <table>
            <thead>
                <tr>
                    <th>Țară</th>
                    <th>Cod</th>
                    <th>An</th>
                    <th>Disciplină</th>
                    <th>Categorie</th>
                    <th>Scor</th>
                </tr>
            </thead>
            <tbody id="resultsBody">
                <tr>    
                    <td colspan="6">Nu există date încă.</td>
                </tr>
            </tbody>
        </table>
    </section>

    <section class="export-card">
        <h2>Export</h2>
        <button id="exportCsv" type="button">CSV</button>
        <button id="exportJson" type="button">JSON</button>
        <button id="exportSvg" type="button">SVG</button>
        <button id="exportWebp" type="button">WebP</button>
    </section>
</main>
<script src="app.js"></script>

</body>
</html>