{{-- Footer --}}
<footer class="mt-5">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <p>&copy; {{ date('Y') }} Dashboard Interaktif. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-end">
                <a href="#" class="me-3">Privacy Policy</a>
                <a href="#" class="me-3">Terms of Service</a>
                <a href="#">Contact Us</a>
            </div>
        </div>
    </div>
</footer>

<style>
    footer {
        border-top: 1px solid #dee2e6;
        margin-top: 50px;
        padding: 1rem 0;
        color: var(--text-muted);
        transition: border-color var(--transition-speed);
    }

    footer a {
        color: var(--text-muted);
        text-decoration: none;
        transition: color var(--transition-speed);
    }

    footer a:hover {
        color: var(--maroon);
    }
</style>
