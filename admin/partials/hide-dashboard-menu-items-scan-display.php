<style>
    #hdmi-scan-overlay {
        position: absolute;
        width: 100%;
        min-height: 100vh;
        height: 100%;
        inset: 0;
        background: rgba(255, 255, 255, 0.95);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        font-size: 18px;
    }

    #hdmi-scan-overlay input[type="submit"] {
        margin-top: 1rem;
        padding: 0.6rem 1.2rem;
        font-size: 20px;
        background-color: #0073aa;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
</style>
<div id="hdmi-scan-overlay">
    <h1>Welcome!</h1>
    <p>
        <strong>Before using this plugin, you need to scan the admin menu.</strong>
    </p>
    <form method="post">
        <input type="hidden" name="hdmi_scan_request" value="1">
        <?php submit_button('Start First Scan', 'primary', '', false); ?>
    </form>
</div>