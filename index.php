<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>3D Model Viewer</title>
    <meta name="description" content="3D viewer for GLB model using modelviewer.dev" />

    <!-- Web Components polyfills for older browsers -->
    <script src="https://unpkg.com/@webcomponents/webcomponentsjs@2.6.0/webcomponents-loader.js"></script>
    <!-- Model Viewer web component (modern and legacy) -->
    <script type="module" src="https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js"></script>
    <script nomodule src="https://unpkg.com/@google/model-viewer/dist/model-viewer-legacy.js"></script>
    <style>
      html, body {
        height: 100%;
        margin: 0;
        background: #f5f6fa;
        color: #111;
        font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji";
      }
      model-viewer {
        width: 100vw;
        height: 100vh;
        display: block;
        background: #f5f6fa;
      }
      .fallback {
        position: absolute;
        inset: 0;
        display: none;
        place-items: center;
        text-align: center;
        padding: 24px;
      }
      model-viewer:not(:defined) .fallback { display: grid; }

      .toolbar {
        position: fixed;
        left: 16px;
        top: 16px;
        display: flex;
        gap: 8px;
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(6px);
        border: 1px solid rgba(0,0,0,0.08);
        padding: 8px;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        z-index: 10;
      }
      .toolbar button {
        appearance: none;
        border: 1px solid rgba(0,0,0,0.12);
        background: #fff;
        color: #111;
        padding: 6px 10px;
        border-radius: 8px;
        font-size: 14px;
        line-height: 1;
        cursor: pointer;
      }
      .toolbar button:hover { background: #f0f2f5; }
    </style>
  </head>
  <body>
    <model-viewer
      src="./P-133-030-rev-C-17.glb"
      alt="3D model"
      camera-controls
      auto-rotate
      rotation-per-second="45deg/s"
      touch-action="pan-y"
      shadow-intensity="1"
      exposure="1.15"
      environment-image="neutral"
      shadow-softness="0.6"
      ar
      ar-modes="webxr scene-viewer"
    >
      <div class="fallback">
        Your browser does not support Web Components. Please use a modern browser to view the 3D model.
      </div>
    </model-viewer>
    <div class="toolbar" role="toolbar" aria-label="Viewer controls">
      <button id="toggle-autorotate" type="button" aria-pressed="true">Auto-rotate</button>
      <button id="zoom-in" type="button" aria-label="Zoom in">Zoom +</button>
      <button id="zoom-out" type="button" aria-label="Zoom out">Zoom −</button>
      <button id="reset" type="button" aria-label="Reset camera">Reset</button>
      <button id="fullscreen" type="button" aria-label="Fullscreen">Fullscreen</button>
    </div>

    <script>
      (function() {
        const viewer = document.querySelector('model-viewer');
        const btnAuto = document.getElementById('toggle-autorotate');
        const btnIn = document.getElementById('zoom-in');
        const btnOut = document.getElementById('zoom-out');
        const btnReset = document.getElementById('reset');
        const btnFs = document.getElementById('fullscreen');

        let initial = null;

        function clamp(val, min, max) { return Math.min(max, Math.max(min, val)); }
        function toOrbitString(theta, phi, radius) { return `${theta}rad ${phi}rad ${radius}m`; }
        function toTargetString(x, y, z) { return `${x}m ${y}m ${z}m`; }

        function wireHandlers() {
          btnAuto.addEventListener('click', () => {
            const enabled = viewer.hasAttribute('auto-rotate');
            if (enabled) {
              viewer.removeAttribute('auto-rotate');
              btnAuto.setAttribute('aria-pressed', 'false');
            } else {
              viewer.setAttribute('auto-rotate', '');
              btnAuto.setAttribute('aria-pressed', 'true');
            }
          });

          btnIn.addEventListener('click', () => {
            const {theta, phi, radius} = viewer.getCameraOrbit();
            const target = clamp(radius * 0.85, 0.01, 1e6);
            viewer.cameraOrbit = toOrbitString(theta, phi, target);
          });

          btnOut.addEventListener('click', () => {
            const {theta, phi, radius} = viewer.getCameraOrbit();
            const target = clamp(radius * 1.15, 0.01, 1e6);
            viewer.cameraOrbit = toOrbitString(theta, phi, target);
          });

          btnReset.addEventListener('click', () => {
            if (!initial) return;
            viewer.cameraTarget = toTargetString(initial.target.x, initial.target.y, initial.target.z);
            viewer.cameraOrbit = toOrbitString(initial.orbit.theta, initial.orbit.phi, initial.orbit.radius);
            viewer.fieldOfView = initial.fov;
          });

          btnFs.addEventListener('click', async () => {
            const root = document.documentElement;
            const exiting = document.fullscreenElement || document.webkitFullscreenElement;
            try {
              if (!exiting) {
                if (root.requestFullscreen) await root.requestFullscreen();
                else if (root.webkitRequestFullscreen) await root.webkitRequestFullscreen();
              } else {
                if (document.exitFullscreen) await document.exitFullscreen();
                else if (document.webkitExitFullscreen) await document.webkitExitFullscreen();
              }
            } catch (e) {
              console.warn('Fullscreen failed', e);
            }
          });
        }

        function captureInitialState() {
          // Safe to call anytime after model load
          const orbit = viewer.getCameraOrbit();
          const target = viewer.getCameraTarget();
          const fov = viewer.fieldOfView; // e.g. "45deg"
          initial = { orbit, target, fov };
        }

        // Ensure the component is defined and model loaded before capturing state
        if (customElements && customElements.whenDefined) {
          customElements.whenDefined('model-viewer').then(() => {
            if (viewer.complete) {
              captureInitialState();
              applyDefaultColor('#2f5597');
            } else {
              viewer.addEventListener('load', () => {
                captureInitialState();
                applyDefaultColor('#2f5597');
              }, { once: true });
            }
            wireHandlers();
          });
        } else {
          // Fallback: wire immediately and capture on load
          if (!viewer) return;
          viewer.addEventListener('load', () => {
            captureInitialState();
            applyDefaultColor('#2f5597');
          }, { once: true });
          wireHandlers();
        }

        function applyDefaultColor(hexColor) {
          try {
            const model = viewer.model;
            if (!model || !model.materials) return;
            for (const material of model.materials) {
              const pbr = material.pbrMetallicRoughness;
              if (!pbr) continue;
              if (typeof pbr.setBaseColorFactor === 'function') {
                pbr.setBaseColorFactor(hexColor);
              } else {
                // Fallback if method name differs in older versions
                pbr.baseColorFactor = hexColor;
              }

              // Make it glossy: higher metallic, lower roughness
              if (typeof pbr.setMetallicFactor === 'function') {
                pbr.setMetallicFactor(0.6);
              } else {
                pbr.metallicFactor = 0.6;
              }
              if (typeof pbr.setRoughnessFactor === 'function') {
                pbr.setRoughnessFactor(0.2);
              } else {
                pbr.roughnessFactor = 0.2;
              }
            }
          } catch (err) {
            console.warn('Failed to set default color', err);
          }
        }
      })();
    </script>
  </body>
  </html>
