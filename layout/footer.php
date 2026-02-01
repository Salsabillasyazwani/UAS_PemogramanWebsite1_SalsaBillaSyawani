<style>
    .main-footer {
        background: #fff;
        padding: 10px 0;
        margin-top: auto;
        margin-left: 316px;
        border-radius: 20px 20px 0 0;
        box-shadow: 0 -4px 20px rgba(0,0,0,0.05);
        border-top: 1px solid #f0f0f0;
    }

    .footer-content {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 30px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    .footer-copyright {
        color: #64748b;
        font-size: 13px;
        font-weight: 500;
        line-height: 1.6;
        text-align: center;
    }

    .footer-copyright b {
        color: #880d0d;
    }

    .footer-tag {
        display: flex;
        align-items: center;
        gap: 12px;
        justify-content: center;
    }

    .badge-uas {
        background: rgba(136, 13, 13, 0.1);
        color: #000000;
        padding: 6px 14px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: 1px solid rgba(136, 13, 13, 0.2);
    }

    @media (max-width: 768px) {
        .main-footer {
            margin-left: 0;
        }
    }
</style>

<footer class="main-footer">
    <div class="footer-content">
        <div class="footer-copyright">
            &copy; <?= date('Y'); ?> <b>23552011402_salsa billa syazwani_TIF-RP 23 CNS B_UASWEB</b>. 
        </div>
        
        </div>
    </div>
</footer>