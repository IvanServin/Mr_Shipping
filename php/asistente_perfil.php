<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="asistente-ia">
    <h3>🎨 Personalizá tu Perfil con IA</h3>
    
    <form method="POST" action="index.php?modulo=api_deepseek">
        <div style="margin:10px 0;">
            <label for="instruccion"><b>Tu descripción:</b></label>
            <textarea 
                id="instruccion" 
                name="instruccion" 
                placeholder="Ej: Quiero que el fondo sea azul..." 
                required 
                style="width:100%; padding:10px; height:80px; margin:5px 0; border:1px solid #ddd; border-radius:5px;"
            ></textarea>
        </div>

        <button type="submit" style="background:#007bff; color:white; padding:10px 20px; border:none; border-radius:5px; cursor:pointer;">
            🎨 Generar Vista Previa
        </button>
    </form>

    <?php if(isset($_SESSION['css_ia'])): ?>
        <div style="margin-top:20px; padding:15px; background:#e8f5e8; border:2px solid #4CAF50; border-radius:5px;">
            <h4>✅ Vista Previa Generada</h4>
            <p>Se creó una vista previa con tus cambios. Revísala antes de aplicar.</p>
            <a href="index.php?modulo=perfil_preview" style="background:#4CAF50; color:white; padding:10px 20px; text-decoration:none; border-radius:5px; display:inline-block; margin-top:10px;">
                👀 Ver Vista Previa
            </a>
        </div>
    <?php endif; ?>
</div>