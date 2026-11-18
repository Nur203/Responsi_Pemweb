// GreenTrack - validasi form dan interaksi sisi klien
(function () {
    'use strict';

    // Menu mobile
    var toggle = document.getElementById('navToggle');
    var nav = document.getElementById('nav');
    if (toggle && nav) {
        toggle.addEventListener('click', function () {
            var open = nav.classList.toggle('open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }

    // Konfirmasi sebelum submit (hapus)
    document.querySelectorAll('form[data-confirm]').forEach(function (f) {
        f.addEventListener('submit', function (ev) {
            if (!window.confirm(f.getAttribute('data-confirm'))) ev.preventDefault();
        });
    });

    // Validasi form umum
    function setError(input, msg) {
        input.classList.add('invalid');
        var label = input.closest('label') || input.parentNode;
        var err = label.querySelector('.field-error');
        if (!err) {
            err = document.createElement('div');
            err.className = 'field-error';
            err.setAttribute('role', 'alert');
            label.appendChild(err);
        }
        err.textContent = msg;
    }
    function clearError(input) {
        input.classList.remove('invalid');
        var label = input.closest('label') || input.parentNode;
        var err = label.querySelector('.field-error');
        if (err) err.remove();
    }

    function validateForm(form) {
        var ok = true;
        form.querySelectorAll('input[required]').forEach(function (inp) {
            clearError(inp);
            var v = inp.value.trim();
            if (v === '') {
                setError(inp, 'Kolom ini wajib diisi.');
                ok = false;
                return;
            }
            if (inp.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) {
                setError(inp, 'Format email tidak valid. Contoh: nama@email.com.');
                ok = false;
                return;
            }
            if (inp.type === 'number') {
                var n = parseFloat(v);
                if (isNaN(n)) { setError(inp, 'Isi dengan angka.'); ok = false; return; }
                if (inp.min !== '' && n < parseFloat(inp.min)) { setError(inp, 'Nilai minimal ' + inp.min + '.'); ok = false; return; }
                if (inp.max !== '' && n > parseFloat(inp.max)) { setError(inp, 'Nilai maksimal ' + inp.max + '.'); ok = false; return; }
            }
            if (inp.type === 'password' && inp.minLength > 0 && v.length < inp.minLength) {
                setError(inp, 'Password minimal ' + inp.minLength + ' karakter.');
                ok = false;
            }
        });

        var p1 = form.querySelector('[name=password], [name=password_baru]');
        var p2 = form.querySelector('[name=password2], [name=password_konf]');
        if (p1 && p2 && p1.value !== p2.value) {
            setError(p2, 'Konfirmasi password tidak cocok.');
            ok = false;
        }
        return ok;
    }

    document.querySelectorAll('form[novalidate]').forEach(function (form) {
        form.addEventListener('submit', function (ev) {
            if (!validateForm(form)) {
                ev.preventDefault();
                var first = form.querySelector('.invalid');
                if (first) first.focus();
            }
        });
        form.querySelectorAll('input').forEach(function (inp) {
            inp.addEventListener('input', function () { clearError(inp); });
        });
    });

    // Pratinjau perhitungan di form pemakaian
    var fp = document.getElementById('formPemakaian');
    if (fp) {
        var tarif = parseFloat(fp.dataset.tarif) || 0;
        var faktor = parseFloat(fp.dataset.faktor) || 0;
        var f = function (n) { return parseFloat(fp.elements[n].value) || 0; };
        var fmt = function (n, d) {
            return n.toLocaleString('id-ID', { minimumFractionDigits: d, maximumFractionDigits: d });
        };
        var update = function () {
            var kwh = (f('daya_watt') * f('durasi_jam') * f('jumlah_hari')) / 1000;
            document.getElementById('pvKwh').textContent = fmt(kwh, 2) + ' kWh';
            document.getElementById('pvBiaya').textContent = 'Rp ' + fmt(kwh * tarif, 0);
            document.getElementById('pvEmisi').textContent = fmt(kwh * faktor, 2) + ' kg CO₂';
        };
        ['daya_watt', 'durasi_jam', 'jumlah_hari'].forEach(function (n) {
            fp.elements[n].addEventListener('input', update);
        });
        update();
    }

    // Grafik batang datar tanpa library eksternal. Batang terakhir (periode terbaru) diberi warna kuning.
    var BULAN = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    function labelPendek(lb) {
        var p = lb.split('-');
        if (p.length === 2) return BULAN[parseInt(p[1], 10) - 1] || lb;
        if (p.length === 3) return p[2] + '/' + p[1];
        return lb;
    }

    function drawBarChart(canvas) {
        var labels = JSON.parse(canvas.dataset.labels || '[]');
        var values = JSON.parse(canvas.dataset.values || '[]');
        if (!labels.length) return;

        var dpr = window.devicePixelRatio || 1;
        var w = canvas.parentNode.clientWidth - 2 * 22;
        if (w < 200) w = 200;
        var h = parseInt(canvas.getAttribute('height'), 10) || 220;
        canvas.style.width = w + 'px';
        canvas.style.height = h + 'px';
        canvas.width = w * dpr;
        canvas.height = h * dpr;
        var ctx = canvas.getContext('2d');
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        ctx.clearRect(0, 0, w, h);

        var padL = 40, padR = 6, padT = 18, padB = 28;
        var cw = w - padL - padR, ch = h - padT - padB;
        var max = Math.max.apply(null, values.concat([1]));
        max = Math.ceil(max * 1.1);

        var font = '12px "Hanken Grotesk", "Segoe UI", sans-serif';
        ctx.font = font;
        ctx.lineWidth = 1;
        ctx.textAlign = 'right';
        for (var i = 0; i <= 4; i++) {
            var y = Math.round(padT + ch - (ch * i / 4)) + 0.5;
            ctx.strokeStyle = i === 0 ? '#16251e' : '#d8ddd2';
            ctx.beginPath(); ctx.moveTo(padL, y); ctx.lineTo(w - padR, y); ctx.stroke();
            ctx.fillStyle = '#5b685f';
            ctx.fillText(Math.round(max * i / 4), padL - 8, y + 4);
        }

        var slot = cw / labels.length;
        var bw = Math.min(44, slot * 0.58);
        var every = Math.ceil(labels.length / Math.max(1, Math.floor(cw / 46)));
        ctx.textAlign = 'center';
        labels.forEach(function (lb, idx) {
            var v = values[idx];
            var bh = (v / max) * ch;
            var x = padL + slot * idx + (slot - bw) / 2;
            var y = padT + ch - bh;
            ctx.fillStyle = idx === labels.length - 1 ? '#e9a820' : '#1d4a37';
            ctx.fillRect(x, y, bw, bh);
            if (v > 0 && labels.length <= 12) {
                ctx.fillStyle = '#16251e';
                ctx.fillText(v.toLocaleString('id-ID', { maximumFractionDigits: 1 }), x + bw / 2, y - 6);
            }
            if (idx % every === 0 || idx === labels.length - 1) {
                ctx.fillStyle = '#5b685f';
                ctx.fillText(labelPendek(lb), x + bw / 2, h - 8);
            }
        });
    }

    function drawAll() {
        document.querySelectorAll('canvas[data-labels]').forEach(drawBarChart);
    }
    window.addEventListener('load', drawAll);
    window.addEventListener('resize', drawAll);
})();
