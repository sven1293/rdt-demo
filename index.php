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
        display: grid;
        grid-template-columns: 350px 1fr;
        height: 100vh;
        gap: 0;
      }

      /* Sidebar Styling */
      .configurator-sidebar {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-right: 1px solid rgba(255, 255, 255, 0.2);
        padding: 24px;
        overflow-y: auto;
        box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
      }

      .sidebar-header {
        text-align: center;
        margin-bottom: 32px;
        padding-bottom: 20px;
        border-bottom: 2px solid #e0e7ff;
      }

      .sidebar-header h1 {
        color: #4f46e5;
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 8px;
      }

      .sidebar-header p {
        color: #6b7280;
        font-size: 14px;
      }

      .config-section {
        margin-bottom: 32px;
        background: #f8fafc;
        border-radius: 12px;
        padding: 20px;
        border: 1px solid #e2e8f0;
      }

      .config-section h3 {
        color: #374151;
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
      }

      .config-section h3::before {
        content: '';
        width: 4px;
        height: 20px;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        border-radius: 2px;
      }

      /* Color Picker Styling */
      .color-options {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 16px;
      }

      .color-option {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 3px solid #fff;
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
      }

      /* Material Picker Styling */
      .material-options {
        display: flex;
        flex-direction: column;
        gap: 12px;
      }

      .material-option {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
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
        width: 32px;
        height: 32px;
        border-radius: 6px;
        border: 1px solid #d1d5db;
      }

      .material-info h4 {
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 4px;
      }

      .material-info p {
        font-size: 12px;
        color: #6b7280;
      }

      /* Slider Styling */
      .slider-group {
        margin-bottom: 16px;
      }

      .slider-label {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
      }

      .slider-label span {
        font-size: 14px;
        font-weight: 500;
        color: #374151;
      }

      .slider-value {
        font-size: 12px;
        color: #6b7280;
        background: #f3f4f6;
        padding: 2px 8px;
        border-radius: 12px;
        min-width: 40px;
        text-align: center;
      }

      .slider {
        width: 100%;
        height: 6px;
        border-radius: 3px;
        background: #e5e7eb;
        outline: none;
        -webkit-appearance: none;
      }

      .slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
      }

      .slider::-moz-range-thumb {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        cursor: pointer;
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
      }

      /* Button Styling */
      .action-buttons {
        display: flex;
        gap: 12px;
        margin-top: 24px;
      }

      .btn {
        flex: 1;
        padding: 12px 16px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
      }

      .btn-primary {
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        color: white;
      }

      .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(79, 70, 229, 0.3);
      }

      .btn-secondary {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #d1d5db;
      }

      .btn-secondary:hover {
        background: #e5e7eb;
      }

      /* 3D Viewer Styling */
      .viewer-container {
        position: relative;
        background: #f8fafc;
      }

      model-viewer {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
      }

      /* Price Display */
      .price-display {
        position: absolute;
        bottom: 20px;
        left: 20px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        padding: 16px 24px;
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
      }

      .price-label {
        font-size: 12px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
      }

      .price-amount {
        font-size: 24px;
        font-weight: 700;
        color: #059669;
      }

      /* Responsive Design */
      @media (max-width: 768px) {
        .app-container {
          grid-template-columns: 1fr;
          grid-template-rows: auto 1fr;
        }
        
        .configurator-sidebar {
          max-height: 400px;
          overflow-y: auto;
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
      <!-- Configurator Sidebar -->
      <div class="configurator-sidebar">
        <div class="sidebar-header">
          <h1>Product Configurator</h1>
          <p>Stel je ideale product samen.</p>
        </div>

        <!-- Kleur Sectie -->
        <div class="config-section">
          <h3>🎨 Kleur</h3>
          <div class="color-options">
            <div class="color-option active" data-color="#9fbbec" style="background: #9fbbec;"></div>
            <div class="color-option" data-color="#2F5597" style="background: #2F5597;"></div>
            <div class="color-option" data-color="#8B4513" style="background: #8B4513;"></div>
            <div class="color-option" data-color="#654321" style="background: #654321;"></div>
            <div class="color-option" data-color="#D2691E" style="background: #D2691E;"></div>
            <div class="color-option" data-color="#F5DEB3" style="background: #F5DEB3;"></div>
            <div class="color-option" data-color="#2F4F4F" style="background: #2F4F4F;"></div>
            <div class="color-option" data-color="#8B0000" style="background: #8B0000;"></div>
            <div class="color-option" data-color="#228B22" style="background: #228B22;"></div>
            <div class="color-option" data-color="#4169E1" style="background: #4169E1;"></div>
          </div>
        </div>

        <!-- Materiaal Sectie -->
        <div class="config-section">
          <h3>🔧 Materiaal</h3>
          <div class="material-options">
            <div class="material-option" data-material="hout">
              <div class="material-preview" style="background: linear-gradient(45deg, #8B4513, #A0522D);"></div>
              <div class="material-info">
                <h4>Natuurhout</h4>
                <p>Klassiek en warm</p>
              </div>
            </div>
            <div class="material-option" data-material="glas">
              <div class="material-preview" style="background: linear-gradient(45deg, #87CEEB, #B0E0E6);"></div>
              <div class="material-info">
                <h4>Glas</h4>
                <p>Modern en elegant</p>
              </div>
            </div>
            <div class="material-option active" data-material="metaal">
              <div class="material-preview" style="background: linear-gradient(45deg, #C0C0C0, #A9A9A9);"></div>
              <div class="material-info">
                <h4>Metaal</h4>
                <p>Industrieel en sterk</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Afmetingen Sectie -->
        <div class="config-section">
          <h3>📏 Afmetingen</h3>
          <div class="slider-group">
            <div class="slider-label">
              <span>Lengte</span>
              <span class="slider-value" id="length-value">120 cm</span>
            </div>
            <input type="range" class="slider" id="length-slider" min="80" max="200" value="120" step="10">
          </div>
          <div class="slider-group">
            <div class="slider-label">
              <span>Breedte</span>
              <span class="slider-value" id="width-value">80 cm</span>
            </div>
            <input type="range" class="slider" id="width-slider" min="60" max="120" value="80" step="10">
          </div>
          <div class="slider-group">
            <div class="slider-label">
              <span>Hoogte</span>
              <span class="slider-value" id="height-value">75 cm</span>
            </div>
            <input type="range" class="slider" id="height-slider" min="60" max="90" value="75" step="5">
          </div>
        </div>

        <!-- Finishing Sectie -->
        <div class="config-section">
          <h3>✨ Afwerking</h3>
          <div class="slider-group">
            <div class="slider-label">
              <span>Glans</span>
              <span class="slider-value" id="gloss-value">50%</span>
            </div>
            <input type="range" class="slider" id="gloss-slider" min="0" max="100" value="50" step="5">
          </div>
          <div class="slider-group">
            <div class="slider-label">
              <span>Textuur</span>
              <span class="slider-value" id="texture-value">30%</span>
            </div>
            <input type="range" class="slider" id="texture-slider" min="0" max="100" value="30" step="5">
          </div>
        </div>

        <!-- Actie Knoppen -->
        <div class="action-buttons">
          <button class="btn btn-secondary" id="reset-config">Reset</button>
          <button class="btn btn-primary" id="save-config">Opslaan</button>
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
            material: 'metaal',
            length: 120,
            width: 80,
            height: 75,
            gloss: 50,
            texture: 30
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

          // Sliders
          const sliders = ['length', 'width', 'height', 'gloss', 'texture'];
          sliders.forEach(sliderName => {
            const slider = document.getElementById(`${sliderName}-slider`);
            const valueDisplay = document.getElementById(`${sliderName}-value`);
            
            slider.addEventListener('input', (e) => {
              const value = parseInt(e.target.value);
              this.currentConfig[sliderName] = value;
              
              // Update display
              if (sliderName === 'gloss' || sliderName === 'texture') {
                valueDisplay.textContent = `${value}%`;
              } else {
                valueDisplay.textContent = `${value} cm`;
              }
              
              this.applyConfiguration();
            });
          });

          // Actie knoppen
          document.getElementById('reset-config').addEventListener('click', () => {
            this.resetConfiguration();
          });

          document.getElementById('save-config').addEventListener('click', () => {
            this.saveConfiguration();
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
                  let clearcoat = 0.0;
                  let clearcoatRoughness = 0.0;

                  switch (this.currentConfig.material) {
                    case 'glas':
                      metallic = 0.0;
                      roughness = 0.05;
                      clearcoat = 0.8;
                      clearcoatRoughness = 0.1;
                      break;
                    case 'metaal':
                      metallic = 0.95;
                      roughness = 0.15;
                      clearcoat = 0.3;
                      clearcoatRoughness = 0.2;
                      break;
                    case 'hout':
                    default:
                      metallic = 0.0;
                      roughness = 0.9;
                      clearcoat = 0.0;
                      clearcoatRoughness = 0.0;
                      break;
                  }

                  // Glans en textuur toepassen met verbeterde berekening
                  const glossFactor = this.currentConfig.gloss / 100;
                  const textureFactor = this.currentConfig.texture / 100;
                  
                  // Pas roughness aan op basis van glans en textuur
                  if (this.currentConfig.material === 'glas') {
                    // Voor glas: glans heeft meer effect
                    roughness = Math.max(0.01, Math.min(0.1, roughness + (textureFactor * 0.05) - (glossFactor * 0.04)));
                  } else if (this.currentConfig.material === 'metaal') {
                    // Voor metaal: balans tussen glans en textuur
                    roughness = Math.max(0.05, Math.min(0.3, roughness + (textureFactor * 0.15) - (glossFactor * 0.1)));
                  } else {
                    // Voor hout: natuurlijke textuur behouden
                    roughness = Math.max(0.7, Math.min(1.0, roughness + (textureFactor * 0.2) - (glossFactor * 0.15)));
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

                    // Clearcoat toepassen als het beschikbaar is (voor glas effect)
                    if (material.clearcoat !== undefined) {
                      material.clearcoat.setFactor(clearcoat);
                    }
                    if (material.clearcoatRoughness !== undefined) {
                      material.clearcoatRoughness.setFactor(clearcoatRoughness);
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

          // Afmetingen kosten
          const sizeMultiplier = (this.currentConfig.length * this.currentConfig.width) / 9600; // 120 * 80 = 9600
          price = Math.round(price * sizeMultiplier);

          // Afwerking kosten
          if (this.currentConfig.gloss > 70) price += 75;
          if (this.currentConfig.texture > 50) price += 50;

          document.getElementById('total-price').textContent = `€ ${price}`;
        }

        resetConfiguration() {
          this.currentConfig = {
            color: '#9fbbec',
            material: 'metaal',
            length: 120,
            width: 80,
            height: 75,
            gloss: 50,
            texture: 30
          };

          // Reset UI
          document.querySelectorAll('.color-option').forEach((opt, index) => {
            opt.classList.toggle('active', index === 0);
          });

          document.querySelectorAll('.material-option').forEach((opt, index) => {
            opt.classList.toggle('active', index === 2);
          });

          // Reset sliders
          const sliders = ['length', 'width', 'height', 'gloss', 'texture'];
          const defaultValues = [120, 80, 75, 50, 30];
          
          sliders.forEach((sliderName, index) => {
            const slider = document.getElementById(`${sliderName}-slider`);
            const valueDisplay = document.getElementById(`${sliderName}-value`);
            
            slider.value = defaultValues[index];
            if (sliderName === 'gloss' || sliderName === 'texture') {
              valueDisplay.textContent = `${defaultValues[index]}%`;
            } else {
              valueDisplay.textContent = `${defaultValues[index]} cm`;
            }
          });

          this.applyConfiguration();
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
