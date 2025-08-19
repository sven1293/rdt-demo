<!doctype html>
<html lang="nl">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <title>Product Configurator</title>
    <meta name="description" content="Product configurator - pas kleuren, materialen en afmetingen aan" />

    <!-- Web Components polyfills for older browsers -->
    <script src="https://unpkg.com/@webcomponents/webcomponentsjs@2.6.0/webcomponents-loader.js"></script>
    <!-- Model Viewer web component -->
    <script type="module" src="https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js"></script>
    <script nomodule src="https://unpkg.com/@google/model-viewer/dist/model-viewer-legacy.js"></script>
    
    <style>
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }

      html, body {
        height: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #333;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        overflow: hidden;
      }

      .app-container {
        display: flex;
        flex-direction: column;
        height: 100vh;
        gap: 0;
      }

      /* Configurator Controls - Boven de 3D viewer */
      .configurator-controls {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        padding: 12px;
        overflow-x: auto;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
        z-index: 10;
      }

      .controls-header {
        text-align: center;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e0e7ff;
      }

      .controls-header h1 {
        color: #4f46e5;
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 4px;
      }

      .controls-header p {
        color: #6b7280;
        font-size: 12px;
      }

      .controls-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 12px;
        max-width: 1200px;
        margin: 0 auto;
      }

      .control-section {
        background: #f8fafc;
        border-radius: 10px;
        padding: 12px;
        border: 1px solid #e2e8f0;
      }

      .control-section h3 {
        color: #374151;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 5px;
      }

      .control-section h3::before {
        content: '';
        width: 2px;
        height: 14px;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        border-radius: 2px;
      }

      /* Color Picker Styling - 4 kleuren */
      .color-options {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
        margin-bottom: 0;
      }

      .color-option {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 2px solid #fff;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        position: relative;
      }

      .color-option:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25);
      }

      .color-option.active {
        border-color: #4f46e5;
        transform: scale(1.15);
      }

      .color-option.active::after {
        content: '✓';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-weight: bold;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
        font-size: 12px;
      }

      /* Materiaal Opties - Vereenvoudigd */
      .material-options {
        display: flex;
        gap: 8px;
      }

      .material-option {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px;
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 12px;
      }

      .material-option:hover {
        border-color: #4f46e5;
        background: #f0f9ff;
      }

      .material-option.active {
        border-color: #4f46e5;
        background: #eff6ff;
      }

      .material-preview {
        width: 20px;
        height: 20px;
        border-radius: 3px;
        border: 1px solid #d1d5db;
      }

      .material-info h4 {
        font-size: 11px;
        font-weight: 600;
        color: #374151;
        margin: 0;
      }

      .material-info p {
        font-size: 9px;
        color: #6b7280;
        margin: 0;
      }

      /* 3D Viewer Styling */
      .viewer-container {
        flex: 1;
        position: relative;
        background: #f8fafc;
        min-height: 0;
      }

      model-viewer {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
      }

      /* Price Display */
      .price-display {
        position: absolute;
        bottom: 12px;
        left: 12px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        padding: 10px 16px;
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
      }

      .price-label {
        font-size: 10px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
      }

      .price-amount {
        font-size: 18px;
        font-weight: 700;
        color: #059669;
      }

      /* Responsive Design - Mobiel First */
      @media (max-width: 768px) {
        .configurator-controls {
          padding: 10px;
        }

        .controls-header h1 {
          font-size: 16px;
        }

        .controls-header p {
          font-size: 11px;
        }

        .controls-grid {
          grid-template-columns: 1fr;
          gap: 10px;
        }

        .control-section {
          padding: 10px;
        }

        .control-section h3 {
          font-size: 12px;
          margin-bottom: 8px;
        }

        .color-options {
          gap: 6px;
        }

        .color-option {
          width: 28px;
          height: 28px;
        }

        .material-options {
          gap: 6px;
        }

        .material-option {
          padding: 6px;
          font-size: 11px;
        }

        .material-preview {
          width: 18px;
          height: 18px;
        }

        .price-display {
          bottom: 10px;
          left: 10px;
          padding: 8px 14px;
        }

        .price-amount {
          font-size: 16px;
        }
      }

      @media (max-width: 480px) {
        .configurator-controls {
          padding: 8px;
        }

        .controls-header {
          margin-bottom: 12px;
        }

        .controls-header h1 {
          font-size: 15px;
        }

        .control-section {
          padding: 8px;
        }

        .color-option {
          width: 26px;
          height: 26px;
        }

        .material-option {
          padding: 5px;
          font-size: 10px;
        }

        .material-preview {
          width: 16px;
          height: 16px;
        }

        .price-display {
          bottom: 8px;
          left: 8px;
          padding: 6px 12px;
        }

        .price-amount {
          font-size: 14px;
        }
      }

      /* Extra kleine schermen (iPhone SE, etc.) */
      @media (max-width: 375px) {
        .configurator-controls {
          padding: 6px;
        }

        .controls-header h1 {
          font-size: 14px;
        }

        .controls-header p {
          font-size: 10px;
        }

        .control-section {
          padding: 6px;
        }

        .color-option {
          width: 24px;
          height: 24px;
        }

        .material-option {
          padding: 4px;
          font-size: 9px;
        }

        .material-preview {
          width: 14px;
          height: 14px;
        }
      }

      /* Loading Animation */
      .loading {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #6b7280;
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(248, 250, 252, 0.9);
        backdrop-filter: blur(4px);
        z-index: 5;
      }

      .loading.hidden {
        display: none;
      }

      .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #e5e7eb;
        border-top: 4px solid #4f46e5;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-right: 16px;
      }

      @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
      }

      .error-message {
        display: none;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #dc2626;
        text-align: center;
        padding: 24px;
        background: rgba(254, 242, 242, 0.9);
        backdrop-filter: blur(4px);
      }

      .error-message.visible {
        display: flex;
        flex-direction: column;
        gap: 16px;
      }

      .error-message h3 {
        color: #dc2626;
        font-size: 18px;
        margin: 0;
      }

      .error-message p {
        color: #7f1d1d;
        margin: 0;
        max-width: 400px;
      }

      .retry-button {
        background: #dc2626;
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
      }

      .retry-button:hover {
        background: #b91c1c;
        transform: translateY(-2px);
      }
    </style>
  </head>
  <body>
    <div class="app-container">
      <!-- Configurator Controls - Boven de 3D viewer -->
      <div class="configurator-controls">
        <div class="controls-header">
          <h1>Product Configurator</h1>
          <p>Stel je ideale product samen.</p>
        </div>

        <div class="controls-grid">
          <!-- Kleur Sectie -->
          <div class="control-section">
            <h3>🎨 Kleur</h3>
            <div class="color-options">
              <div class="color-option active" data-color="#9fbbec" style="background: #9fbbec;"></div>
              <div class="color-option" data-color="#2F5597" style="background: #2F5597;"></div>
              <div class="color-option" data-color="#8B4513" style="background: #8B4513;"></div>
              <div class="color-option" data-color="#654321" style="background: #654321;"></div>
            </div>
          </div>

          <!-- Materiaal Sectie -->
          <div class="control-section">
            <h3>🔧 Materiaal</h3>
            <div class="material-options">
              <div class="material-option" data-material="hout">
                <div class="material-preview" style="background: linear-gradient(45deg, #8B4513, #A0522D);"></div>
                <div class="material-info">
                  <h4>Hout</h4>
                  <p>Natuurlijk</p>
                </div>
              </div>
              <div class="material-option" data-material="glas">
                <div class="material-preview" style="background: linear-gradient(45deg, #87CEEB, #B0E0E6);"></div>
                <div class="material-info">
                  <h4>Glas</h4>
                  <p>Modern</p>
                </div>
              </div>
              <div class="material-option active" data-material="metaal">
                <div class="material-preview" style="background: linear-gradient(45deg, #C0C0C0, #A9A9A9);"></div>
                <div class="material-info">
                  <h4>Metaal</h4>
                  <p>Industrieel</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 3D Viewer Container -->
      <div class="viewer-container">
        <model-viewer
          src="./P-133-030-rev-C-17.glb"
          alt="3D Tafel Model"
          camera-controls
          auto-rotate
          rotation-per-second="60deg/s"
          touch-action="none"
          shadow-intensity="1"
          exposure="1.2"
          environment-image="neutral"
          shadow-softness="0.8"
          ar
          ar-modes="webxr scene-viewer"
          loading="eager"
        >
          <div class="loading" id="loading-overlay">
            <div class="spinner"></div>
            <span>3D Model laden...</span>
          </div>
          
          <div class="error-message" id="error-message">
            <h3>❌ Fout bij laden</h3>
            <p id="error-text">Er is een fout opgetreden bij het laden van het 3D model.</p>
            <button class="retry-button" onclick="location.reload()">Opnieuw proberen</button>
          </div>
        </model-viewer>

        <!-- Prijs Display -->
        <div class="price-display">
          <div class="price-label">Totaalprijs</div>
          <div class="price-amount" id="total-price">€ 599</div>
        </div>
      </div>
    </div>

    <script>
      class TableConfigurator {
        constructor() {
          this.viewer = null;
          this.currentConfig = {
            color: '#9fbbec',
            material: 'metaal'
          };
          this.basePrice = 599;
          this.init();
        }

        init() {
          this.waitForModelViewer().then(() => {
            this.setupViewer();
            this.setupEventListeners();
            this.applyConfiguration();
            
            // Start auto-rotate onmiddellijk na initialisatie
            if (this.viewer) {
              this.viewer.setAttribute('auto-rotate', '');
              // Zorg ervoor dat de toggle-rotate knop actief is
              const toggleBtn = document.getElementById('toggle-rotate');
              if (toggleBtn) {
                toggleBtn.classList.add('active');
              }
            }
          });
        }

        async waitForModelViewer() {
          if (customElements.get('model-viewer')) {
            return Promise.resolve();
          }
          return new Promise(resolve => {
            customElements.whenDefined('model-viewer').then(resolve);
          });
        }

        setupViewer() {
          this.viewer = document.querySelector('model-viewer');
          
          // Zorg ervoor dat auto-rotate standaard aan staat
          this.viewer.setAttribute('auto-rotate', '');
          
          // Start auto-rotate onmiddellijk
          setTimeout(() => {
            this.viewer.setAttribute('auto-rotate', '');
            // Zorg ervoor dat de toggle-rotate knop actief is
            const toggleBtn = document.getElementById('toggle-rotate');
            if (toggleBtn) {
              toggleBtn.classList.add('active');
            }
          }, 100);
          
          // Timeout voor het laden
          const loadingTimeout = setTimeout(() => {
            if (this.viewer && !this.viewer.model) {
              this.showErrorOverlay('Het 3D model duurt te lang om te laden. Controleer of het bestand bestaat en probeer het opnieuw.');
            }
          }, 10000); // 10 seconden timeout
          
          this.viewer.addEventListener('load', () => {
            clearTimeout(loadingTimeout);
            console.log('3D Model geladen');
            this.hideLoadingOverlay();
            this.hideErrorOverlay();
            this.applyConfiguration();
            
            // Start auto-rotate na het laden
            this.viewer.setAttribute('auto-rotate', '');
            
            // Zorg ervoor dat de toggle-rotate knop actief is
            const toggleBtn = document.getElementById('toggle-rotate');
            if (toggleBtn) {
              toggleBtn.classList.add('active');
            }
          });

          this.viewer.addEventListener('error', (error) => {
            clearTimeout(loadingTimeout);
            console.error('Fout bij laden 3D model:', error);
            this.hideLoadingOverlay();
            this.showErrorOverlay('Fout bij laden 3D model. Controleer of het bestand P-133-030-rev-C-17.glb bestaat.');
          });

          // Extra check voor model loading
          this.viewer.addEventListener('model-visibility', () => {
            if (this.viewer.model) {
              clearTimeout(loadingTimeout);
              this.hideLoadingOverlay();
              this.hideErrorOverlay();
              this.applyConfiguration();
            }
          });
        }

        setupEventListeners() {
          // Kleur opties
          document.querySelectorAll('.color-option').forEach(option => {
            option.addEventListener('click', (e) => {
              document.querySelectorAll('.color-option').forEach(opt => opt.classList.remove('active'));
              e.target.classList.add('active');
              this.currentConfig.color = e.target.dataset.color;
              
              // Kleine vertraging voor soepelere kleur switching
              setTimeout(() => {
                this.applyConfiguration();
              }, 30);
            });
          });

          // Materiaal opties
          document.querySelectorAll('.material-option').forEach(option => {
            option.addEventListener('click', (e) => {
              document.querySelectorAll('.material-option').forEach(opt => opt.classList.remove('active'));
              e.target.closest('.material-option').classList.add('active');
              this.currentConfig.material = e.target.closest('.material-option').dataset.material;
              
              // Kleine vertraging voor soepelere materiaal switching
              setTimeout(() => {
                this.applyConfiguration();
              }, 50);
            });
          });
        }

        applyConfiguration() {
          if (!this.viewer || !this.viewer.model) return;

          try {
            const model = this.viewer.model;
            
            // Pas kleur en materiaal eigenschappen toe
            if (model.materials) {
              model.materials.forEach(material => {
                if (material.pbrMetallicRoughness) {
                  const pbr = material.pbrMetallicRoughness;
                  
                  // Kleur toepassen
                  if (pbr.setBaseColorFactor) {
                    pbr.setBaseColorFactor(this.currentConfig.color);
                  } else {
                    pbr.baseColorFactor = this.currentConfig.color;
                  }

                  // Materiaal eigenschappen toepassen - verbeterde logica
                  let metallic = 0.1;
                  let roughness = 0.8;

                  switch (this.currentConfig.material) {
                    case 'glas':
                      metallic = 0.0;
                      roughness = 0.05;
                      break;
                    case 'metaal':
                      metallic = 0.95;
                      roughness = 0.15;
                      break;
                    case 'hout':
                    default:
                      metallic = 0.0;
                      roughness = 0.9;
                      break;
                  }

                  // Pas eigenschappen toe met fallback
                  try {
                    if (pbr.setMetallicFactor) {
                      pbr.setMetallicFactor(metallic);
                    } else {
                      pbr.metallicFactor = metallic;
                    }

                    if (pbr.setRoughnessFactor) {
                      pbr.setRoughnessFactor(roughness);
                    } else {
                      pbr.roughnessFactor = roughness;
                    }
                  } catch (e) {
                    console.warn('Kon niet alle materiaal eigenschappen toepassen:', e);
                  }
                }
              });
            }

            // Force render update
            if (this.viewer.renderer) {
              this.viewer.renderer.render(this.viewer.scene, this.viewer.camera);
            }

            // Update prijs
            this.updatePrice();
            
          } catch (error) {
            console.warn('Kon configuratie niet toepassen:', error);
          }
        }

        updatePrice() {
          let price = this.basePrice;
          
          // Materiaal kosten
          switch (this.currentConfig.material) {
            case 'glas':
              price += 150;
              break;
            case 'metaal':
              price += 100;
              break;
            case 'hout':
            default:
              price += 50;
              break;
          }

          document.getElementById('total-price').textContent = `€ ${price}`;
        }

        saveConfiguration() {
          const configData = {
            ...this.currentConfig,
            price: document.getElementById('total-price').textContent,
            timestamp: new Date().toISOString()
          };

          // Maak downloadbare configuratie
          const dataStr = JSON.stringify(configData, null, 2);
          const dataBlob = new Blob([dataStr], { type: 'application/json' });
          
          const link = document.createElement('a');
          link.href = URL.createObjectURL(dataBlob);
          link.download = 'tafel-configuratie.json';
          link.click();

          // Toon bevestiging
          alert('Configuratie opgeslagen! Je kunt het bestand downloaden.');
        }

        showErrorOverlay(message) {
          const errorMessageElement = document.getElementById('error-message');
          const errorTextElement = document.getElementById('error-text');
          if (errorMessageElement && errorTextElement) {
            errorTextElement.textContent = message;
            errorMessageElement.classList.add('visible');
            this.hideLoadingOverlay();
          }
        }

        hideErrorOverlay() {
          const errorMessageElement = document.getElementById('error-message');
          if (errorMessageElement) {
            errorMessageElement.classList.remove('visible');
            errorMessageElement.classList.add('hidden');
          }
        }

        hideLoadingOverlay() {
          const loadingOverlay = document.getElementById('loading-overlay');
          if (loadingOverlay) {
            loadingOverlay.style.display = 'none';
          }
        }
      }

      // Start de configurator wanneer de pagina geladen is
      document.addEventListener('DOMContentLoaded', () => {
        new TableConfigurator();
      });
    </script>
  </body>
</html>
