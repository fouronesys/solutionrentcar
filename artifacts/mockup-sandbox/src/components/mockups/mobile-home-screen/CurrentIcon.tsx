import React from "react";

const apps = [
  { label: "Mensajes", tone: "messages", symbol: "•••" },
  { label: "Cámara", tone: "camera", symbol: "◉" },
  { label: "Mapas", tone: "maps", symbol: "⌖" },
  { label: "Música", tone: "music", symbol: "♫" },
  { label: "Fotos", tone: "photos", symbol: "✺" },
  { label: "Correo", tone: "mail", symbol: "✉" },
  { label: "Notas", tone: "notes", symbol: "≡" },
  { label: "Clima", tone: "weather", symbol: "☼" },
  { label: "Wallet", tone: "wallet", symbol: "▣" },
  { label: "Ajustes", tone: "settings", symbol: "⚙" },
  { label: "Calendario", tone: "calendar", symbol: "29" },
  { label: "Safari", tone: "safari", symbol: "◒" },
];

const dockApps = [
  { label: "Teléfono", tone: "phone", symbol: "⌕" },
  { label: "WhatsApp", tone: "whatsapp", symbol: "◔" },
  { label: "Chrome", tone: "chrome", symbol: "●" },
  { label: "Spotify", tone: "spotify", symbol: "≋" },
];

function AppTile({
  label,
  tone,
  symbol,
  featured = false,
}: {
  label: string;
  tone: string;
  symbol: string;
  featured?: boolean;
}) {
  return (
    <div className={`app-tile ${featured ? "featured" : ""}`}>
      <div className={`app-icon ${tone}`}>
        {featured ? (
          <img
            src="/__mockup/images/casa-rivas-icon.png"
            alt="Icono de Casa Rivas RentCar"
          />
        ) : (
          <span>{symbol}</span>
        )}
      </div>
      <div className="app-label">{label}</div>
    </div>
  );
}

export function CurrentIcon() {
  return (
    <main className="preview-shell">
      <div className="phone-frame">
        <div className="dynamic-island" />
        <div className="status-bar">
          <span>9:41</span>
          <div className="status-icons">
            <span className="signal">▮▮▮</span>
            <span>⌁</span>
            <span className="battery">▰</span>
          </div>
        </div>

        <section className="home-screen">
          <div className="date-line">VIERNES, 29 DE AGOSTO</div>
          <div className="widget-row">
            <div className="widget calendar-widget">
              <span className="widget-kicker">PRÓXIMO VIAJE</span>
              <strong>Casa Rivas</strong>
              <span className="widget-detail">Tu próxima reserva está lista</span>
              <span className="widget-arrow">›</span>
            </div>
            <div className="widget weather-widget">
              <span className="weather-temp">28°</span>
              <span className="widget-detail">Santo Domingo</span>
              <span className="weather-icon">☀</span>
            </div>
          </div>

          <div className="app-grid">
            <AppTile {...apps[0]} />
            <AppTile {...apps[1]} />
            <AppTile {...apps[2]} />
            <AppTile {...apps[3]} />
            <AppTile
              label="Casa Rivas"
              tone="rentcar"
              symbol=""
              featured
            />
            <AppTile {...apps[4]} />
            <AppTile {...apps[5]} />
            <AppTile {...apps[6]} />
            <AppTile {...apps[7]} />
            <AppTile {...apps[8]} />
            <AppTile {...apps[9]} />
            <AppTile {...apps[10]} />
            <AppTile {...apps[11]} />
          </div>

          <div className="page-dots">
            <span className="active" />
            <span />
            <span />
          </div>

          <div className="dock">
            {dockApps.map((app) => (
              <AppTile key={app.label} {...app} />
            ))}
          </div>
        </section>

        <div className="home-indicator" />
      </div>

      <style>{`
        * { box-sizing: border-box; }
        html, body, #root { margin: 0; min-width: 100%; min-height: 100%; }
        body {
          background: #151311;
          color: #fff;
          font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "Segoe UI", sans-serif;
        }
        .preview-shell {
          min-height: 900px;
          display: flex;
          justify-content: center;
          align-items: center;
          padding: 24px;
          background:
            radial-gradient(circle at 50% 0%, rgba(209, 159, 60, .16), transparent 28%),
            #151311;
        }
        .phone-frame {
          position: relative;
          width: 390px;
          height: 844px;
          overflow: hidden;
          border: 7px solid #24211e;
          border-radius: 48px;
          background: #111315;
          box-shadow:
            0 30px 70px rgba(0, 0, 0, .45),
            0 0 0 1px rgba(255, 255, 255, .07);
        }
        .phone-frame::before {
          content: "";
          position: absolute;
          inset: 0;
          pointer-events: none;
          z-index: 2;
          background: linear-gradient(118deg, rgba(255,255,255,.07), transparent 21%, transparent 72%, rgba(255,255,255,.04));
        }
        .dynamic-island {
          position: absolute;
          top: 10px;
          left: 50%;
          z-index: 4;
          width: 116px;
          height: 30px;
          transform: translateX(-50%);
          border-radius: 20px;
          background: #050505;
        }
        .status-bar {
          position: absolute;
          top: 11px;
          left: 24px;
          right: 24px;
          z-index: 3;
          display: flex;
          justify-content: space-between;
          align-items: center;
          padding-top: 1px;
          font-size: 14px;
          font-weight: 650;
          letter-spacing: -.2px;
        }
        .status-icons {
          display: flex;
          align-items: center;
          gap: 7px;
          font-size: 13px;
        }
        .signal { font-size: 10px; letter-spacing: -2px; }
        .battery { font-size: 14px; transform: scaleX(1.15); }
        .home-screen {
          position: absolute;
          inset: 0;
          padding: 64px 16px 24px;
          background:
            radial-gradient(ellipse at 20% 10%, rgba(99, 135, 167, .72), transparent 34%),
            radial-gradient(ellipse at 87% 27%, rgba(180, 135, 79, .75), transparent 36%),
            radial-gradient(ellipse at 44% 87%, rgba(89, 71, 100, .75), transparent 40%),
            linear-gradient(145deg, #233a50 0%, #121a28 39%, #493b3c 100%);
        }
        .home-screen::before {
          content: "";
          position: absolute;
          inset: 0;
          opacity: .32;
          background:
            linear-gradient(122deg, transparent 8%, rgba(255, 216, 159, .22) 18%, transparent 29%),
            radial-gradient(ellipse at 64% 58%, rgba(255, 255, 255, .12), transparent 36%);
          mix-blend-mode: screen;
        }
        .date-line, .widget-row, .app-grid, .page-dots, .dock {
          position: relative;
          z-index: 1;
        }
        .date-line {
          margin: 3px 4px 10px;
          color: rgba(255, 255, 255, .9);
          font-size: 11px;
          font-weight: 700;
          letter-spacing: .8px;
        }
        .widget-row {
          display: grid;
          grid-template-columns: 1.32fr 1fr;
          gap: 10px;
          margin-bottom: 18px;
        }
        .widget {
          position: relative;
          height: 102px;
          overflow: hidden;
          padding: 14px;
          border: 1px solid rgba(255,255,255,.15);
          border-radius: 19px;
          background: rgba(26, 27, 31, .53);
          box-shadow: inset 0 1px rgba(255,255,255,.08);
          backdrop-filter: blur(16px);
        }
        .widget-kicker {
          display: block;
          color: #e5b758;
          font-size: 8px;
          font-weight: 800;
          letter-spacing: .8px;
        }
        .widget strong {
          display: block;
          margin-top: 7px;
          font-family: Georgia, serif;
          font-size: 16px;
          letter-spacing: -.2px;
        }
        .widget-detail {
          display: block;
          margin-top: 4px;
          color: rgba(255,255,255,.68);
          font-size: 9px;
        }
        .widget-arrow {
          position: absolute;
          right: 12px;
          bottom: 10px;
          color: #e3b552;
          font-size: 21px;
        }
        .weather-widget {
          display: flex;
          flex-direction: column;
          justify-content: center;
        }
        .weather-temp {
          font-size: 28px;
          font-weight: 300;
          letter-spacing: -1.5px;
        }
        .weather-icon {
          position: absolute;
          top: 13px;
          right: 13px;
          color: #f1c25d;
          font-size: 23px;
        }
        .app-grid {
          display: grid;
          grid-template-columns: repeat(4, 1fr);
          row-gap: 17px;
          column-gap: 5px;
        }
        .app-tile {
          min-width: 0;
          display: flex;
          flex-direction: column;
          align-items: center;
          gap: 5px;
        }
        .app-icon {
          display: grid;
          place-items: center;
          width: 62px;
          height: 62px;
          overflow: hidden;
          border-radius: 16px;
          color: #fff;
          font-size: 29px;
          font-weight: 700;
          box-shadow: 0 5px 10px rgba(0,0,0,.25), inset 0 1px rgba(255,255,255,.3);
        }
        .app-icon img {
          width: 100%;
          height: 100%;
          object-fit: cover;
          display: block;
        }
        .app-label {
          max-width: 82px;
          overflow: hidden;
          color: rgba(255,255,255,.95);
          font-size: 10px;
          line-height: 13px;
          text-align: center;
          text-overflow: ellipsis;
          white-space: nowrap;
          text-shadow: 0 1px 3px rgba(0,0,0,.8);
        }
        .rentcar .app-icon, .app-icon.rentcar {
          box-shadow:
            0 0 0 1px rgba(227,181,82,.5),
            0 5px 14px rgba(0,0,0,.4),
            0 0 16px rgba(212,159,48,.23);
        }
        .messages { background: linear-gradient(145deg, #36d77c, #16994d); }
        .camera { background: radial-gradient(circle at 35% 28%, #777, #1d1d23 55%, #050505); }
        .maps { background: linear-gradient(145deg, #82d68d 0 34%, #f3cf69 35% 66%, #a7cdf4 67%); color: #2a6b43; }
        .music { background: linear-gradient(145deg, #fa87aa, #9548ce); }
        .photos { background: #f6f6f3; color: #f05a64; }
        .mail { background: linear-gradient(145deg, #58bdf4, #2776dc); }
        .notes { background: linear-gradient(#ffe26f 0 18%, #f5e8bb 19% 100%); color: #8e6c20; }
        .weather { background: linear-gradient(145deg, #55b8f4, #2976cf); }
        .wallet { background: linear-gradient(145deg, #bb7cec, #5f34ab); }
        .settings { background: linear-gradient(145deg, #9199a4, #4b525f); }
        .calendar { background: #f6f5f2; color: #ef4e57; font-size: 17px; }
        .safari { background: radial-gradient(circle, #d6f4ff 0 15%, #38a9eb 16% 55%, #1d74c7 56%); }
        .phone { background: linear-gradient(145deg, #36dc80, #19874e); }
        .whatsapp { background: linear-gradient(145deg, #42dc7c, #12944d); }
        .chrome { background: conic-gradient(#e14e40 0 33%, #f8ce5d 33% 66%, #47ad6a 66%); color: #3477c4; }
        .spotify { background: linear-gradient(145deg, #54d579, #159148); }
        .page-dots {
          display: flex;
          justify-content: center;
          gap: 6px;
          margin-top: 20px;
        }
        .page-dots span {
          width: 5px;
          height: 5px;
          border-radius: 50%;
          background: rgba(255,255,255,.42);
        }
        .page-dots .active { background: #fff; }
        .dock {
          display: grid;
          grid-template-columns: repeat(4, 1fr);
          gap: 9px;
          margin-top: 17px;
          padding: 11px 8px 8px;
          border: 1px solid rgba(255,255,255,.13);
          border-radius: 25px;
          background: rgba(30, 29, 32, .48);
          backdrop-filter: blur(18px);
        }
        .dock .app-icon { width: 61px; height: 61px; }
        .home-indicator {
          position: absolute;
          bottom: 8px;
          left: 50%;
          z-index: 3;
          width: 120px;
          height: 4px;
          transform: translateX(-50%);
          border-radius: 3px;
          background: rgba(255,255,255,.92);
        }
      `}</style>
    </main>
  );
}