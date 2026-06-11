<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BLUESKY — Adhésion confirmée</title>
</head>
<body style="margin:0;padding:0;background:#F0F9FF;font-family:'Segoe UI',Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#F0F9FF;padding:32px 16px;">
  <tr>
    <td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 4px 24px rgba(2,132,199,0.10);">

        <!-- Header gradient -->
        <tr>
          <td style="background:linear-gradient(135deg,#0284C7 0%,#0EA5E9 50%,#38BDF8 100%);padding:40px 40px 32px;text-align:center;">
            <div style="display:inline-block;background:rgba(255,255,255,0.15);border-radius:50%;padding:16px;margin-bottom:16px;">
              <img src="https://i.imgur.com/placeholder.png" alt="" width="64" height="64"
                   style="display:block;border-radius:50%;"
                   onerror="this.style.display='none'">
            </div>
            <div style="font-size:28px;font-weight:900;color:#ffffff;letter-spacing:3px;margin-bottom:4px;">BLUESKY</div>
            <div style="font-size:11px;color:rgba(255,255,255,0.75);letter-spacing:3px;text-transform:uppercase;">TRANSACTIONS</div>

            <!-- Checkmark badge -->
            <div style="margin-top:28px;display:inline-block;background:rgba(255,255,255,0.2);border:2px solid rgba(255,255,255,0.4);border-radius:50%;width:72px;height:72px;line-height:72px;font-size:36px;text-align:center;">
              ✅
            </div>
            <div style="margin-top:14px;font-size:22px;font-weight:800;color:#ffffff;line-height:1.3;">
              Adhésion confirmée !
            </div>
            <div style="font-size:14px;color:rgba(255,255,255,0.85);margin-top:6px;">
              Votre compte agent BLUESKY est maintenant actif
            </div>
          </td>
        </tr>

        <!-- Body -->
        <tr>
          <td style="padding:36px 40px;">

            <p style="margin:0 0 20px;font-size:16px;color:#1E293B;line-height:1.6;">
              Bonjour <strong style="color:#0284C7;">{{ $agent->name }}</strong>,
            </p>
            <p style="margin:0 0 24px;font-size:15px;color:#475569;line-height:1.7;">
              Nous avons le plaisir de vous informer que votre compte agent a été <strong>validé et activé</strong>
              par l'administration BLUESKY. Vous faites désormais partie de notre réseau de transfert d'argent en Afrique.
            </p>

            <!-- Agent code card -->
            <table width="100%" cellpadding="0" cellspacing="0" style="background:linear-gradient(135deg,#EFF6FF,#F0F9FF);border:2px solid #BAE6FD;border-radius:16px;margin-bottom:28px;">
              <tr>
                <td style="padding:24px;text-align:center;">
                  <div style="font-size:11px;font-weight:700;color:#0284C7;letter-spacing:2px;text-transform:uppercase;margin-bottom:10px;">
                    Votre code agent
                  </div>
                  <div style="font-size:34px;font-weight:900;font-family:'Courier New',monospace;color:#0369A1;letter-spacing:10px;background:#ffffff;border-radius:12px;padding:14px 24px;display:inline-block;box-shadow:0 2px 10px rgba(2,132,199,0.15);">
                    {{ $agent->agent_code }}
                  </div>
                  <div style="font-size:12px;color:#64748B;margin-top:10px;">
                    Conservez ce code — il vous identifie sur la plateforme
                  </div>
                </td>
              </tr>
            </table>

            <!-- Info grid -->
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
              <tr>
                <td style="padding:0 8px 12px 0;" width="50%">
                  <table width="100%" cellpadding="0" cellspacing="0" style="background:#F8FAFC;border-radius:12px;border:1px solid #E2E8F0;">
                    <tr>
                      <td style="padding:16px;">
                        <div style="font-size:20px;margin-bottom:6px;">👤</div>
                        <div style="font-size:11px;color:#94A3B8;text-transform:uppercase;letter-spacing:1px;margin-bottom:4px;">Nom</div>
                        <div style="font-size:14px;font-weight:600;color:#1E293B;">{{ $agent->name }}</div>
                      </td>
                    </tr>
                  </table>
                </td>
                <td style="padding:0 0 12px 8px;" width="50%">
                  <table width="100%" cellpadding="0" cellspacing="0" style="background:#F8FAFC;border-radius:12px;border:1px solid #E2E8F0;">
                    <tr>
                      <td style="padding:16px;">
                        <div style="font-size:20px;margin-bottom:6px;">📧</div>
                        <div style="font-size:11px;color:#94A3B8;text-transform:uppercase;letter-spacing:1px;margin-bottom:4px;">Email</div>
                        <div style="font-size:13px;font-weight:600;color:#1E293B;word-break:break-all;">{{ $agent->email }}</div>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td style="padding:0 8px 0 0;" width="50%">
                  <table width="100%" cellpadding="0" cellspacing="0" style="background:#F8FAFC;border-radius:12px;border:1px solid #E2E8F0;">
                    <tr>
                      <td style="padding:16px;">
                        <div style="font-size:20px;margin-bottom:6px;">🌍</div>
                        <div style="font-size:11px;color:#94A3B8;text-transform:uppercase;letter-spacing:1px;margin-bottom:4px;">Pays d'opération</div>
                        <div style="font-size:14px;font-weight:600;color:#1E293B;">
                          {{ $agent->country->name ?? '—' }}
                        </div>
                      </td>
                    </tr>
                  </table>
                </td>
                <td style="padding:0 0 0 8px;" width="50%">
                  <table width="100%" cellpadding="0" cellspacing="0" style="background:#F8FAFC;border-radius:12px;border:1px solid #E2E8F0;">
                    <tr>
                      <td style="padding:16px;">
                        <div style="font-size:20px;margin-bottom:6px;">📅</div>
                        <div style="font-size:11px;color:#94A3B8;text-transform:uppercase;letter-spacing:1px;margin-bottom:4px;">Membre depuis</div>
                        <div style="font-size:14px;font-weight:600;color:#1E293B;">
                          {{ $agent->created_at->translatedFormat('d M Y') }}
                        </div>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>

            <!-- What you can do -->
            <table width="100%" cellpadding="0" cellspacing="0" style="background:#ECFDF5;border:1px solid #A7F3D0;border-radius:14px;margin-bottom:28px;">
              <tr>
                <td style="padding:22px 24px;">
                  <div style="font-size:13px;font-weight:700;color:#065F46;margin-bottom:14px;">
                    ✅ Ce que vous pouvez maintenant faire :
                  </div>
                  <div style="font-size:13px;color:#047857;line-height:2;">
                    &bull; &nbsp;Enregistrer des transactions d'envoi et de retrait<br>
                    &bull; &nbsp;Consulter et gérer votre historique de transactions<br>
                    &bull; &nbsp;Accéder à votre tableau de bord agent personnalisé<br>
                    &bull; &nbsp;Exporter vos données au format CSV<br>
                    &bull; &nbsp;Gérer votre profil et vos paramètres
                  </div>
                </td>
              </tr>
            </table>

            <!-- Security warning -->
            <table width="100%" cellpadding="0" cellspacing="0" style="background:#FEF2F2;border:1px solid #FECACA;border-radius:14px;margin-bottom:28px;">
              <tr>
                <td style="padding:18px 22px;">
                  <div style="font-size:13px;font-weight:700;color:#991B1B;margin-bottom:8px;">
                    🔒 Sécurité de votre compte
                  </div>
                  <div style="font-size:13px;color:#B91C1C;line-height:1.65;">
                    Ne partagez jamais votre mot de passe. BLUESKY ne vous demandera jamais vos identifiants
                    par email ou téléphone. En cas d'activité suspecte, contactez immédiatement l'administration.
                  </div>
                </td>
              </tr>
            </table>

            <p style="margin:0 0 24px;font-size:14px;color:#64748B;line-height:1.7;text-align:center;">
              Si vous avez des questions, n'hésitez pas à contacter notre équipe de support.
            </p>

            <!-- CTA Button -->
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:8px;">
              <tr>
                <td align="center">
                  <a href="#" style="display:inline-block;background:linear-gradient(135deg,#0284C7,#0EA5E9);color:#ffffff;font-size:15px;font-weight:700;padding:14px 40px;border-radius:50px;text-decoration:none;letter-spacing:0.5px;box-shadow:0 4px 14px rgba(2,132,199,0.35);">
                    🚀 &nbsp;Accéder à ma plateforme
                  </a>
                </td>
              </tr>
            </table>

          </td>
        </tr>

        <!-- Footer -->
        <tr>
          <td style="background:#F8FAFC;border-top:1px solid #E2E8F0;padding:24px 40px;text-align:center;">
            <div style="font-size:13px;font-weight:700;color:#0284C7;letter-spacing:2px;margin-bottom:6px;">BLUESKY TRANSACTIONS</div>
            <div style="font-size:12px;color:#94A3B8;line-height:1.6;">
              Votre partenaire de confiance pour les transferts d'argent en Afrique<br>
              DR Congo &bull; Zambie &bull; Tanzanie &bull; Kenya &bull; Malawi &bull; Zimbabwe &bull; Afrique du Sud &bull; Namibie
            </div>
            <div style="margin-top:14px;font-size:11px;color:#CBD5E1;">
              Cet email a été envoyé automatiquement par BLUESKY Transactions. Merci de ne pas y répondre.
            </div>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>

</body>
</html>
