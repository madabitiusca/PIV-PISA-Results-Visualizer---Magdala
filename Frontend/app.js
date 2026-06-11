const countrySelect = document.getElementById('country');
const disciplineSelect = document.getElementById('discipline');
const subjectSelect = document.getElementById('subject');
const yearSelect = document.getElementById('year');
const searchBtn = document.getElementById('searchBtn');

const resultsBody = document.getElementById('resultsBody');
const resultCount = document.getElementById('resultCount');
const avgScore = document.getElementById('avgScore');
const selectedCountry = document.getElementById('selectedCountry');

async function loadCountries() {
try{
    const response = await fetch('/Api/countries.php');
    const result = await response.json();

    countrySelect.innerHTML = '';

    result.data.forEach(country => {
        const option = document.createElement('option');
        option.value = country.code;
        option.textContent = country.name;
        countrySelect.appendChild(option);
    });
} catch (error) {
        console.error('Could not load countries:', error);

        countrySelect.innerHTML = `
            <option value="">Could not load countries</option>
        `;
    }
}

function buildPisaUrl() {
    const params = new URLSearchParams();

    params.append('country', countrySelect.value);
    params.append('discipline', disciplineSelect.value);
    params.append('subject', subjectSelect.value);

    if (yearSelect.value !== '') {
        params.append('year', yearSelect.value);
    }

return `/Api/pisa.php?${params.toString()}`;}

async function loadPisaData() {
    const response = await fetch(buildPisaUrl());
    const result = await response.json();
    const data = result.data || [];

    renderTable(data);
    renderSummary(data);
    renderLineChart(data);
    renderBarChart();
    renderRadarChart();
}


function renderTable(data) {
    resultsBody.innerHTML = '';

    if (data.length === 0) {
        resultsBody.innerHTML = `
            <tr>
                <td colspan="6">Nu există rezultate.</td>
            </tr>
        `;
        return;
    }

    data.forEach(row => {
        const tr = document.createElement('tr');

        tr.innerHTML = `
            <td>${escapeHtml(row.country ?? '')}</td>
            <td>${escapeHtml(row.country_code)}</td>
            <td>${escapeHtml(row.year)}</td>
            <td>${escapeHtml(row.discipline)}</td>
            <td>${escapeHtml(row.subject)}</td>
            <td>${escapeHtml(row.score)}</td>
        `;

        resultsBody.appendChild(tr);
    });
}

function renderSummary(data) {
    resultCount.textContent = data.length;

    if (data.length === 0) {
        avgScore.textContent = '-';
        selectedCountry.textContent = '-';
        return;
    }

    const sum = data.reduce((acc, row) => acc + Number(row.score), 0);

    avgScore.textContent = (sum / data.length).toFixed(2);
    selectedCountry.textContent = data[0].country ?? data[0].country_code;
}

function escapeHtml(value) {
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

searchBtn.addEventListener('click', loadPisaData);

loadCountries().then(loadPisaData);

function renderLineChart(data) {
    const svg = document.getElementById('lineChart');
    svg.innerHTML = '';

    if (data.length === 0) {
        return;
    }

    const sorted = [...data].sort((a, b) => Number(a.year) - Number(b.year));

    const width = 800;
    const height = 360;
    const padding = 50;

    const scores = sorted.map(row => Number(row.score));
    const years = sorted.map(row => Number(row.year));

    const minScore = Math.min(...scores) - 10;
    const maxScore = Math.max(...scores) + 10;
    const minYear = Math.min(...years);
    const maxYear = Math.max(...years);

    const points = sorted.map(row => {
        const year = Number(row.year);
        const score = Number(row.score);

        const x = padding + ((year - minYear) / (maxYear - minYear || 1)) * (width - 2 * padding);
        const y = height - padding - ((score - minScore) / (maxScore - minScore || 1)) * (height - 2 * padding);

        return { x, y, year, score };
    });

    const linePoints = points
        .map(point => `${point.x},${point.y}`)
        .join(' ');

    svg.innerHTML = `
        <line x1="${padding}" y1="${height - padding}" x2="${width - padding}" y2="${height - padding}" stroke="#334155" />
        <line x1="${padding}" y1="${padding}" x2="${padding}" y2="${height - padding}" stroke="#334155" />

        <polyline
            points="${linePoints}"
            fill="none"
            stroke="#2563eb"
            stroke-width="4"
        />

        ${points.map(point => `
            <circle cx="${point.x}" cy="${point.y}" r="6" fill="#2563eb"></circle>
            <text x="${point.x - 18}" y="${point.y - 12}" font-size="13">${point.score}</text>
            <text x="${point.x - 18}" y="${height - 20}" font-size="13">${point.year}</text>
        `).join('')}
    `;
}

async function renderBarChart() {
    const svg = document.getElementById('barChart');
    svg.innerHTML = '';

    const countries = ['AUS', 'AUT', 'FRA', 'DEU', 'USA'];

    const params = new URLSearchParams();
    params.append('countries', countries.join(','));
    params.append('discipline', disciplineSelect.value);
    params.append('subject', subjectSelect.value);

    if (yearSelect.value !== '') {
        params.append('year', yearSelect.value);
    } else {
        params.append('year', '2018');
    }

    const response = await fetch(`/Api/compare.php?${params.toString()}`);
    const result = await response.json();
    const data = result.data || [];

    if (data.length === 0) {
        return;
    }

    const width = 800;
    const height = 360;
    const padding = 50;

    const maxScore = Math.max(...data.map(row => Number(row.score))) + 20;
    const barWidth = (width - 2 * padding) / data.length - 20;

    svg.innerHTML = `
        <line x1="${padding}" y1="${height - padding}" x2="${width - padding}" y2="${height - padding}" stroke="#334155" />
        <line x1="${padding}" y1="${padding}" x2="${padding}" y2="${height - padding}" stroke="#334155" />
    `;

    data.forEach((row, index) => {
        const score = Number(row.score);
        const barHeight = (score / maxScore) * (height - 2 * padding);
        const x = padding + index * (barWidth + 20);
        const y = height - padding - barHeight;

        svg.innerHTML += `
            <rect x="${x}" y="${y}" width="${barWidth}" height="${barHeight}" rx="8" fill="#2563eb"></rect>
            <text x="${x}" y="${y - 8}" font-size="13">${score}</text>
            <text x="${x}" y="${height - 22}" font-size="13">${row.country_code}</text>
        `;
    });
}

async function renderRadarChart() {
    const svg = document.getElementById('radarChart');
    svg.innerHTML = '';

    const params = new URLSearchParams();

    params.append('country', countrySelect.value);
    params.append('subject', subjectSelect.value);

    if (yearSelect.value !== '') {
        params.append('year', yearSelect.value);
    } else {
        params.append('year', '2018');
    }

    const response = await fetch(`/Api/pisa.php?${params.toString()}`);
    const result = await response.json();
    const data = result.data || [];

    const disciplines = ['math', 'reading', 'science'];

    const values = disciplines.map(discipline => {
        const row = data.find(item => item.discipline === discipline);
        return row ? Number(row.score) : 0;
    });

    if (values.every(value => value === 0)) {
        return;
    }

    const width = 800;
    const height = 360;
    const centerX = width / 2;
    const centerY = height / 2;
    const maxRadius = 120;
    const maxScore = 600;

    const angleStep = (Math.PI * 2) / disciplines.length;

    function getPoint(index, value) {
        const angle = -Math.PI / 2 + index * angleStep;
        const radius = (value / maxScore) * maxRadius;

        return {
            x: centerX + Math.cos(angle) * radius,
            y: centerY + Math.sin(angle) * radius
        };
    }

    function getAxisPoint(index) {
        const angle = -Math.PI / 2 + index * angleStep;

        return {
            x: centerX + Math.cos(angle) * maxRadius,
            y: centerY + Math.sin(angle) * maxRadius
        };
    }

    const polygonPoints = values
        .map((value, index) => {
            const point = getPoint(index, value);
            return `${point.x},${point.y}`;
        })
        .join(' ');

    svg.innerHTML = `
        <circle cx="${centerX}" cy="${centerY}" r="${maxRadius}" fill="none" stroke="#cbd5e1"></circle>
        <circle cx="${centerX}" cy="${centerY}" r="${maxRadius * 0.66}" fill="none" stroke="#e2e8f0"></circle>
        <circle cx="${centerX}" cy="${centerY}" r="${maxRadius * 0.33}" fill="none" stroke="#e2e8f0"></circle>

        ${disciplines.map((discipline, index) => {
            const axis = getAxisPoint(index);

            return `
                <line x1="${centerX}" y1="${centerY}" x2="${axis.x}" y2="${axis.y}" stroke="#94a3b8"></line>
                <text x="${axis.x - 35}" y="${axis.y - 10}" font-size="14">${discipline}</text>
            `;
        }).join('')}

        <polygon points="${polygonPoints}" fill="rgba(37, 99, 235, 0.25)" stroke="#2563eb" stroke-width="3"></polygon>

        ${values.map((value, index) => {
            const point = getPoint(index, value);

            return `
                <circle cx="${point.x}" cy="${point.y}" r="5" fill="#2563eb"></circle>
                <text x="${point.x + 8}" y="${point.y}" font-size="13">${value}</text>
            `;
        }).join('')}
    `;
}