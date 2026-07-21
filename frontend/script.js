const API_BASE = '/api/';

// Tab switching
document.querySelectorAll('nav button').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('nav button').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
        document.getElementById('tab-' + this.dataset.tab).classList.add('active');
    });
});

// ========== PROXY ==========
document.getElementById('btnStartProxy').addEventListener('click', async () => {
    const port = document.getElementById('proxyPort').value;
    const res = await fetch(API_BASE + 'proxy/start', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({port: parseInt(port)})
    });
    const data = await res.json();
    document.getElementById('status').textContent = '● Running on :' + port;
    alert('Proxy started');
});

document.getElementById('btnStopProxy').addEventListener('click', async () => {
    await fetch(API_BASE + 'proxy/stop', {method: 'POST'});
    document.getElementById('status').textContent = '● Stopped';
    alert('Proxy stopped');
});

document.getElementById('toggleIntercept').addEventListener('change', async function() {
    const enabled = this.checked;
    await fetch(API_BASE + 'proxy/intercept', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({enabled})
    });
    document.getElementById('interceptLabel').textContent = enabled ? 'ON' : 'OFF';
});

async function loadCaptured() {
    const res = await fetch(API_BASE + 'proxy/capture');
    const data = await res.json();
    const list = document.getElementById('capturedList');
    if (data.length === 0) {
        list.innerHTML = '<p style="color:#888;">No requests captured</p>';
        return;
    }
    list.innerHTML = data.map((req, i) =>
        `<div style="border-bottom:1px solid #2a2a4a;padding:6px 0;">
            #${i+1} ${req.method || 'GET'} ${req.url || ''}
            <span style="color:#888;float:right;">${req.timestamp || ''}</span>
        </div>`
    ).join('');
}
setInterval(loadCaptured, 5000);
loadCaptured();

// ========== REPEATER ==========
document.getElementById('btnSendRep').addEventListener('click', async () => {
    const url = document.getElementById('repUrl').value;
    const method = document.getElementById('repMethod').value;
    const headers = JSON.parse(document.getElementById('repHeaders').value || '{}');
    const body = document.getElementById('repBody').value;
    const res = await fetch(API_BASE + 'repeater/send', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({url, method, headers, body})
    });
    const data = await res.json();
    document.getElementById('repResponse').textContent = 'Status: ' + data.status + '\n\n' + data.response;
});

// ========== INTRUDER ==========
document.getElementById('btnStartInt').addEventListener('click', async () => {
    const url = document.getElementById('intUrl').value;
    const otpField = document.getElementById('intOtpField').value;
    const threads = parseInt(document.getElementById('intThreads').value);
    const res = await fetch(API_BASE + 'intruder/start', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({url, otp_field: otpField, threads})
    });
    const data = await res.json();
    const out = document.getElementById('intResults');
    if (data.found) {
        out.innerHTML = '✅ FOUND OTP: ' + data.found + '\nAttempts: ' + data.attempts;
    } else {
        out.innerHTML = '❌ Not found. Attempts: ' + data.attempts;
    }
});

// ========== BYPASS ==========
document.getElementById('btnRunBypass').addEventListener('click', async () => {
    const url = document.getElementById('byUrl').value;
    const res = await fetch(API_BASE + 'bypass/run', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({url})
    });
    const data = await res.json();
    const out = document.getElementById('byResults');
    out.innerHTML = Object.entries(data).map(([key, val]) =>
        key + ': ' + (val.success ? '✅' : '❌') + ' (HTTP ' + val.http + ')'
    ).join('\n');
});

// ========== SSL ==========
document.getElementById('btnGenCA').addEventListener('click', async () => {
    const res = await fetch(API_BASE + 'ssl/generate', {method: 'POST'});
    const data = await res.json();
    document.getElementById('sslStatus').textContent = '✅ ' + data.message;
});

document.getElementById('btnDownloadCA').addEventListener('click', () => {
    window.location.href = API_BASE + 'ssl/download';
});

document.getElementById('btnGenDomain').addEventListener('click', async () => {
    const domain = document.getElementById('sslDomain').value;
    if (!domain) {
        alert('Enter domain name');
        return;
    }
    const res = await fetch(API_BASE + 'ssl/domain', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({domain})
    });
    const data = await res.json();
    document.getElementById('sslStatus').textContent = '✅ ' + data.message;
});
