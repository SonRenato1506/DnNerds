<footer class="footer">
    <div class="footer-container">

        <p>
            © <?= date('Y') ?> DnNerds —
            Renato Matos, Natalia Macedo, Arthur Simões,
            Diego Toscano, Yuri Reis e Matheus
        </p>

        <div class="footer-links">

            <a href="https://www.youtube.com/" target="_blank" title="YouTube">
                <img src="../Imagens/youtube.png" alt="YouTube">
            </a>

            <a href="https://www.instagram.com/DnNerds" target="_blank" title="Instagram">
                <img src="../Imagens/instagram.jpeg" alt="Instagram">
            </a>

            <a href="https://www.facebook.com/" target="_blank" title="Facebook">
                <img src="../Imagens/facebook.png" alt="Facebook">
            </a>

            <a href="https://www.tiktok.com/" target="_blank" title="TikTok">
                <img src="../Imagens/tiktok.jpeg" alt="TikTok">
            </a>

        </div>
    </div>
</footer>

<style>
    /* ===================== */
    /* ⚙️ Footer */
    .footer {
        background: rgb(32, 32, 32);
        color: white;
        padding: 10px 20px;
        text-align: center;
        font-family: "Anonymous Pro", monospace;
        position: relative;
        box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.6);
    }

    /* Linha decorativa */
    .footer::before {
        content: "";
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 80%;
        height: 2px;
        background: linear-gradient(90deg,
                transparent,
                var(--texto-header),
                transparent);
        opacity: 0.4;
    }

    /* Container */
    .footer-container {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 15px;
    }

    /* Texto */
    .footer p {
        font-size: 0.95rem;
        line-height: 1.5;
        opacity: 0.85;
    }

    /* Ícones */
    .footer-links {
        display: flex;
        gap: 18px;
        margin-top: 10px;
    }

    .footer-links a img {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        padding: 6px;
        transition: transform 0.3s ease, filter 0.3s ease;
    }

    /* Hover */
    .footer-links a img:hover {
        transform: translateY(-4px) scale(1.1);
        filter: brightness(1.2);
    }
</style>