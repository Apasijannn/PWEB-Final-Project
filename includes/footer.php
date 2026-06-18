<style>
    :root {
        --primary-color: #1a2a6c;
        --accent-color: #f2a341;
    }
    
    /* Membuat footer selalu di bawah meskipun konten sedikit */
    html, body {
        height: 100%;
    }
    body {
        display: flex;
        flex-direction: column;
        background-color: #fcfcfc;
        font-family: 'Segoe UI', sans-serif;
    }
    main {
        flex: 1; /* Ini yang mendorong footer ke bawah */
    }

    .navbar { background-color: var(--primary-color); }
    
    /* Style khusus Footer */
    .footer {
        background-color: #ffffff;
        border-top: 1px solid #eee;
        padding: 2      0px 0;
        color: #6c757d;
    }
    
    /* Style Link Login Tersembunyi */
    .staff-link {
        color: #e0e0e0; /* Warna sangat samar agar tidak mencolok */
        text-decoration: none;
        font-size: 11px;
        transition: 0.3s;
    }
    .staff-link:hover {
        color: var(--accent-color); /* Baru terlihat jelas saat di-hover */
    }

    html, body {
        height: 100%;
    }
    body {
        display: flex;
        flex-direction: column;
    }
    main {
        flex: 1; /* Mendorong footer ke bawah */
    }

    .footer {
        background-color: #ffffff;
        border-top: 1px solid #eee;
        padding: 30px 0; /* Memberi ruang lebih lega */
        color: #6c757d;
        text-align: center; /* Membuat semua teks di dalamnya ke tengah */
    }
    
    .staff-link {
        color: #e0e0e0;
        text-decoration: none;
        font-size: 11px;
        display: block; /* Agar link berada di baris baru */
        margin-top: 10px;
        transition: 0.3s;
    }
    .staff-link:hover {
        color: var(--accent-color);
    }
</style>

</main> <footer class="footer mt-auto">
        <div class="container">
            <p class="mb-0">&copy; 2026 <strong>Pat-Pat Cafe</strong>. All Rights Reserved.</p>
            <small class="d-block text-secondary mt-1">Surabaya, Indonesia</small>
            
            <a href="admin/login.php" class="staff-link">Staff Access</a>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>