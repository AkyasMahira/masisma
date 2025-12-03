<style>
    /* --- Footer Container --- */
    .footer-minimal {
        background-color: rgba(255,255,255,0.7); /* Hitam pekat/minimalis */
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        padding: 1rem 0; /* Padding kecil agar tidak tinggi */
        color: #6c757d; /* Warna teks abu-abu agar tidak mencolok */
        font-size: 0.85rem; /* Font kecil */
        font-family: sans-serif;
        margin-top: auto;
    }

    .footer-content {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 20px; /* Jarak antar elemen */
        flex-wrap: wrap; /* Agar aman di mobile */
    }

    /* --- Links --- */
    .footer-link {
        color: #6c757d;
        text-decoration: none;
        transition: color 0.2s ease;
    }
    .footer-link:hover {
        color: #a3191d; /* Putih saat hover */
    }

    /* --- Divider Kecil --- */
    .footer-dot {
        width: 4px; height: 4px;
        background-color: #333;
        border-radius: 50%;
    }

    /* --- WA Link (Minimalis) --- */
    .wa-minimal {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #6c757d;
        text-decoration: none;
        transition: color 0.2s ease;
        font-weight: 500;
    }
    .wa-minimal:hover {
        color: #25D366; /* Hijau WA hanya saat hover */
    }
    .wa-minimal svg {
        fill: currentColor;
        transition: transform 0.2s;
    }
    .wa-minimal:hover svg {
        transform: scale(1.1);
    }
</style>

<footer class="footer-minimal">
    <div class="container">
        <div class="footer-content">
            
            <!-- Copyright -->
            <div>
                &copy; {{ date('Y') }} 
                <a href="https://akyas-bio.vercel.app" target="_blank" class="footer-link fw-bold">Sindikat
                <span class="mx-1">·</span> All Rights Reserved
                </a>
            </div>

            <!-- Dot Separator (Hilang di HP jika layar sempit) -->
            <div class="footer-dot d-none d-md-block"></div>

            <!-- Helpdesk -->
            <div>
                <a href="https://wa.me/6282245415977" target="_blank" class="wa-minimal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16 16">
                        <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/>
                    </svg>
                    Help Desk
                </a>
            </div>

        </div>
    </div>
</footer>