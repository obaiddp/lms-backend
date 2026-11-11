<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Donation Receipt</title>
</head>
<body style="margin:0; padding:0; background-color:#efefef; font-family: Georgia, 'Times New Roman', serif; color:#222222;">
  <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:#efefef; margin:0; padding:0;">
    <tr>
      <td align="center" style="padding:0;">
        
        <!-- Main Container -->
        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="800" style="width:800px; max-width:800px; background-color:#efefef;">
          
          <!-- Top spacing -->
          <tr>
            <td style="height:18px; line-height:18px; font-size:0;">&nbsp;</td>
          </tr>

          <!-- Logo -->
          <tr>
            <td align="center" style="padding:0 20px 8px 20px;">
              <img 
                src="{{ $logo_url ?? 'https://yourdomain.com/path-to-logo.png' }}" 
                alt="Character Education Foundation" 
                style="display:block; max-width:260px; width:260px; height:auto; border:0;"
              />
            </td>
          </tr>

          <!-- Organization Name -->
          <tr>
            <td align="center" style="padding:8px 40px 0 40px; font-size:24px; line-height:32px; font-weight:bold; color:#3f6f3f;">
              Character Education Foundation
            </td>
          </tr>

          <!-- Tagline -->
          <tr>
            <td align="center" style="padding:8px 55px 0 55px; font-size:12px; line-height:20px; font-style:italic; color:#666666;">
              "Transforming Society for a Bright Future through Quran &amp; Sunnah, Character Building, Community Development, Youth Leadership, and Mentor Development Programs"
            </td>
          </tr>

          <!-- Divider -->
          <tr>
            <td style="padding:22px 42px 16px 42px;">
              <div style="height:3px; background-color:#3f6f3f; line-height:3px; font-size:0;">&nbsp;</div>
            </td>
          </tr>

          <!-- Title -->
          <tr>
            <td align="center" style="padding:8px 20px 18px 20px; font-size:22px; line-height:30px; font-weight:bold; letter-spacing:1px; color:#3f6f3f;">
              DONATION RECEIPT
            </td>
          </tr>

          <!-- Date / Receipt -->
          <tr>
            <td style="padding:0 54px 8px 54px; font-size:15px; line-height:26px; color:#111111;">
              <strong>Date:</strong> {{ $receipt_date }}<br />
              <strong>Receipt No:</strong> {{ $receipt_no }}
            </td>
          </tr>

          <!-- Received with thanks -->
          <tr>
            <td style="padding:18px 54px 12px 54px; font-size:15px; line-height:24px; font-weight:bold; color:#111111;">
              Received with thanks from:
            </td>
          </tr>

          <!-- Donor Details Table -->
          <tr>
            <td style="padding:0 54px 18px 54px;">
              <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse; width:100%; background-color:#efefef;">
                <tr>
                  <td width="35%" style="border:1px solid #cfcfcf; padding:12px 12px; font-size:14px; line-height:20px; font-weight:bold; color:#444444;">Donor Name</td>
                  <td width="65%" style="border:1px solid #cfcfcf; padding:12px 12px; font-size:14px; line-height:20px; color:#222222;">{{ $donor_name }}</td>
                </tr>
                <tr>
                  <td style="border:1px solid #cfcfcf; padding:12px 12px; font-size:14px; line-height:20px; font-weight:bold; color:#444444;">Contact#</td>
                  <td style="border:1px solid #cfcfcf; padding:12px 12px; font-size:14px; line-height:20px; color:#222222;">{{ $contact_number }}</td>
                </tr>
                <tr>
                  <td style="border:1px solid #cfcfcf; padding:12px 12px; font-size:14px; line-height:20px; font-weight:bold; color:#444444;">E-mail</td>
                  <td style="border:1px solid #cfcfcf; padding:12px 12px; font-size:14px; line-height:20px; color:#222222;">{{ $email }}</td>
                </tr>
                <tr>
                  <td style="border:1px solid #cfcfcf; padding:12px 12px; font-size:14px; line-height:20px; font-weight:bold; color:#444444;">Address</td>
                  <td style="border:1px solid #cfcfcf; padding:12px 12px; font-size:14px; line-height:24px; color:#222222;">
                    {{ $address_line_1 }}<br />
                    {{ $address_line_2 }}
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Payment Details -->
          <tr>
            <td style="padding:12px 54px 12px 54px; font-size:15px; line-height:24px; font-weight:bold; color:#111111;">
              Payment Details:
            </td>
          </tr>

          <tr>
            <td style="padding:0 54px 18px 54px;">
              <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse; width:100%; background-color:#efefef;">
                <tr>
                  <td width="35%" style="border:1px solid #cfcfcf; padding:12px 12px; font-size:14px; line-height:20px; font-weight:bold; color:#444444;">Payment Reference</td>
                  <td width="65%" style="border:1px solid #cfcfcf; padding:12px 12px; font-size:14px; line-height:20px; color:#222222;">{{ $payment_reference }}</td>
                </tr>
                <tr>
                  <td style="border:1px solid #cfcfcf; padding:12px 12px; font-size:14px; line-height:20px; font-weight:bold; color:#444444;">Payment Date</td>
                  <td style="border:1px solid #cfcfcf; padding:12px 12px; font-size:14px; line-height:20px; color:#222222;">{{ $payment_date }}</td>
                </tr>
                <tr>
                  <td style="border:1px solid #cfcfcf; padding:12px 12px; font-size:14px; line-height:20px; font-weight:bold; color:#444444;">Payment Method</td>
                  <td style="border:1px solid #cfcfcf; padding:12px 12px; font-size:14px; line-height:20px; color:#222222;">{{ $payment_method }}</td>
                </tr>
                <tr>
                  <td style="border:1px solid #cfcfcf; padding:12px 12px; font-size:14px; line-height:20px; font-weight:bold; color:#444444;">Amount Received</td>
                  <td style="border:1px solid #cfcfcf; padding:12px 12px; font-size:14px; line-height:20px; font-weight:bold; color:#111111;">{{ $currency }} {{ $amount_received }}</td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Allocation Details -->
          <tr>
            <td style="padding:12px 54px 12px 54px; font-size:15px; line-height:24px; font-weight:bold; color:#111111;">
              Allocation Details:
            </td>
          </tr>

          <tr>
            <td style="padding:0 54px 20px 54px;">
              <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse; width:100%;">
                <tr style="background-color:#2f652d;">
                  <td width="5%" style="border:1px solid #4f754c; padding:12px 10px; font-size:14px; line-height:20px; font-weight:bold; color:#ffffff;">#</td>
                  <td width="45%" style="border:1px solid #4f754c; padding:12px 10px; font-size:14px; line-height:20px; font-weight:bold; color:#ffffff;">Program/Product</td>
                  <td width="25%" style="border:1px solid #4f754c; padding:12px 10px; font-size:14px; line-height:20px; font-weight:bold; color:#ffffff;">Allocation Type</td>
                  <td width="25%" align="right" style="border:1px solid #4f754c; padding:12px 10px; font-size:14px; line-height:20px; font-weight:bold; color:#ffffff;">Amount</td>
                </tr>

                <!-- Repeat rows dynamically -->
                <tr>
                  @foreach($allocations ?? [] as $index => $row)
                <tr>
                  <td style="border:1px solid #cfcfcf; padding:11px 10px; font-size:14px; line-height:20px; color:#222222;">{{ $index + 1 }}</td>
                  <td style="border:1px solid #cfcfcf; padding:11px 10px; font-size:14px; line-height:20px; color:#222222;">{{ $row['program'] ?? '' }}</td>
                  <td style="border:1px solid #cfcfcf; padding:11px 10px; font-size:14px; line-height:20px; color:#222222;">{{ $row['type'] ?? '' }}</td>
                  <td align="right" style="border:1px solid #cfcfcf; padding:11px 10px; font-size:14px; line-height:20px; color:#222222;">{{ $currency }} {{ $row['amount'] ?? '0.00' }}</td>
                </tr>
                @endforeach

                <tr style="background-color:#dbe5d9;">
                  <td colspan="3" align="right" style="border:1px solid #cfcfcf; padding:12px 10px; font-size:14px; line-height:20px; font-weight:bold; color:#111111;">
                    Total Amount:
                  </td>
                  <td align="right" style="border:1px solid #cfcfcf; padding:12px 10px; font-size:14px; line-height:20px; font-weight:bold; color:#111111;">
                    {{ $currency }} {{ $total_amount }}
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Quote / Thank you box -->
          <tr>
            <td style="padding:8px 54px 22px 54px;">
              <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse; width:100%; background-color:#e6ece4; border-left:4px solid #2f652d;">
                <tr>
                  <td style="padding:18px 18px 14px 18px; font-size:14px; line-height:24px; color:#3f5f3d; font-style:italic;">
                    <strong>Thank you for your generous contribution!</strong><br /><br />
                    The Example of Those who Spend Their Wealth in the Cause of Allah is that of a Grain that Sprouts into Seven Ears, Each Bearing One Hundred Grains. And Allah Multiplies (the Reward Even More) to Whoever He Wills. For Allah is All-Bountiful, All-Knowing. (Al Baqrah - 261)
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Computer generated note -->
          <tr>
            <td style="padding:8px 54px 30px 54px; font-size:14px; line-height:24px; font-style:italic; font-weight:bold; color:#111111;">
              (This is a computer-generated receipt &amp; does not require signature or stamp)
            </td>
          </tr>

          <!-- Query line -->
          <tr>
            <td align="center" style="padding:0 54px 36px 54px; font-size:12px; line-height:20px; color:#777777;">
              For any queries regarding this receipt, please contact us at finance@cef.org.pk or call +92 (051) 843 55 53-4
            </td>
          </tr>

          <!-- Bottom divider -->
          <tr>
            <td style="padding:0 42px 20px 42px;">
              <div style="height:3px; background-color:#3f6f3f; line-height:3px; font-size:0;">&nbsp;</div>
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td align="center" style="padding:0 55px 6px 55px; font-size:11px; line-height:20px; color:#555555;">
              A Not-for-Profit Company Setup under Section 42 of the Companies Act, 2017
            </td>
          </tr>
          <tr>
            <td align="center" style="padding:0 55px 6px 55px; font-size:11px; line-height:20px; color:#555555;">
              Approved &amp; Tax Exempted NPO from Pakistan's Federal Board of Revenue (FBR) u/s 2(36) of ITO
            </td>
          </tr>
          <tr>
            <td align="center" style="padding:0 55px 6px 55px; font-size:11px; line-height:20px; color:#555555;">
              Certified NPO with Pakistan Center of Philanthropy (PCP) under Certificate PCP-R2-2025/864 - Dated 19-May-2025
            </td>
          </tr>
          <tr>
            <td align="center" style="padding:0 55px 6px 55px; font-size:11px; line-height:20px; color:#555555;">
              National Tax Number (NTN): 56 46 197 - 2 | SECP Registration Number (CUI): 01 37 231
            </td>
          </tr>
          <tr>
            <td align="center" style="padding:0 55px 6px 55px; font-size:11px; line-height:20px; color:#555555;">
              2nd Floor, Landmark Heights, Service Road East, Ghori Town, ISB Expressway, Islamabad
            </td>
          </tr>
          <tr>
            <td align="center" style="padding:0 55px 6px 55px; font-size:11px; line-height:20px; color:#555555;">
              Tel: +92 (05) 843 55 53-4 | Email: accounts@cef.org.pk | Web: https://www.cef.org.pk/
            </td>
          </tr>
          <tr>
            <td align="center" style="padding:0 55px 24px 55px; font-size:12px; line-height:20px; color:#555555;">
              &copy; 2026 Character Education Foundation. All rights reserved.
            </td>
          </tr>
        </table>
        <!-- /Main Container -->

      </td>
    </tr>
  </table>
</body>
</html>