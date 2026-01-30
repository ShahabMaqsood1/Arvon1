# 📧 Complete Email & DNS Configuration Guide

## Email Flow Architecture

```
Contact Form Submission
        ↓
FROM: no-reply@arvon.pk (automated sender)
TO: info@arvon.pk (you receive)
REPLY-TO: customer@email.com (when you hit reply)
        ↓
Your Email Client (Gmail/Outlook)
```

**This ensures:**
- ✅ Professional sender address
- ✅ No emails to yourself
- ✅ Easy reply to customers
- ✅ Better deliverability

---

## Step 1: Spacemail Account Setup

### Create Email Accounts

1. **Login to Spacemail Dashboard**
   - URL: https://spacemail.com/login
   - Use your Spacemail credentials

2. **Add Domain**
   - Click "Add Domain"
   - Enter: `arvon.pk`
   - Verify ownership (they'll provide instructions)

3. **Create Email Accounts**

   **Account 1: no-reply@arvon.pk**
   - Purpose: Send automated emails
   - Password: Generate strong password
   - Storage: 500MB (sufficient for logs)
   - Note: You never check this inbox

   **Account 2: info@arvon.pk**
   - Purpose: Receive customer inquiries
   - Password: Generate strong password
   - Storage: 2GB+ recommended
   - Note: Your main inbox

4. **Get SMTP Credentials**
   - Server: `smtp.spacemail.com`
   - Port: `587` (TLS)
   - Username: `info@arvon.pk`
   - Password: [Your Spacemail password]

5. **Get DKIM Key**
   - In Spacemail → Domain Settings → DKIM
   - Copy the DKIM record (long string)
   - You'll add this to DNS

---

## Step 2: DNS Configuration

### Where to Add DNS Records

**Option A: Domain Registrar**
- GoDaddy → DNS Management
- Namecheap → Advanced DNS
- HostGator → DNS Zone Editor

**Option B: cPanel**
- cPanel → Zone Editor

### DNS Records to Add

#### 1. SPF Record (Sender Policy Framework)

**Prevents email spoofing**

```
Type: TXT
Host: @ (or arvon.pk)
Value: v=spf1 include:_spf.spacemail.com ~all
TTL: 3600
```

**What this does:** Tells receiving servers that Spacemail is authorized to send emails for arvon.pk

#### 2. DKIM Record (DomainKeys Identified Mail)

**Authenticates your emails**

```
Type: TXT
Host: spacemail._domainkey
Value: [GET FROM SPACEMAIL DASHBOARD]
TTL: 3600
```

**Example DKIM value:**
```
v=DKIM1; k=rsa; p=MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC...
```

**What this does:** Adds a digital signature to your emails proving they're legitimate

#### 3. DMARC Record (Domain-based Message Authentication)

**Tells servers what to do with failed authentication**

```
Type: TXT
Host: _dmarc
Value: v=DMARC1; p=quarantine; rua=mailto:dmarc@arvon.pk; pct=100
TTL: 3600
```

**What this does:** 
- `p=quarantine`: Suspicious emails go to spam
- `rua=mailto:dmarc@arvon.pk`: Send reports here
- `pct=100`: Apply to 100% of emails

#### 4. MX Records (Mail Exchange)

**Routes incoming mail to Spacemail**

```
Priority: 10
Type: MX
Host: @
Points to: mx1.spacemail.com
TTL: 3600
```

```
Priority: 20
Type: MX
Host: @
Points to: mx2.spacemail.com
TTL: 3600
```

---

## Step 3: Website Configuration

### Update config.php

```php
// In /config.php
define('SMTP_HOST', 'smtp.spacemail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'info@arvon.pk');
define('SMTP_PASSWORD', 'your_spacemail_password_here');
define('SMTP_FROM_EMAIL', 'no-reply@arvon.pk');
define('SMTP_FROM_NAME', 'ARVON Website');
define('ADMIN_EMAIL', 'info@arvon.pk');
```

---

## Step 4: Testing Email Deliverability

### Test 1: Basic Functionality

1. Go to: `https://arvon.pk/contact.php`
2. Fill form with YOUR email address
3. Submit
4. Check info@arvon.pk inbox
5. Click "Reply" - should go to YOUR email

### Test 2: Spam Score

1. Go to: https://www.mail-tester.com/
2. Copy the test email address shown
3. Submit contact form using that address
4. Check your score (aim for 10/10)

**If score < 10:**
- Wait 24 hours for DNS propagation
- Verify all DNS records added correctly
- Check SPF/DKIM/DMARC alignment

### Test 3: Major Providers

Send test emails to:
- ✅ Gmail: test@gmail.com
- ✅ Outlook: test@outlook.com  
- ✅ Yahoo: test@yahoo.com

**Check:**
- Arrives in inbox (not spam)
- Reply-To works correctly
- No warning messages

---

## Step 5: Monitoring & Maintenance

### Check Email Logs

**In Spacemail Dashboard:**
- View sent emails
- Delivery status
- Bounce rates
- Spam complaints

### DMARC Reports

You'll receive weekly reports at `dmarc@arvon.pk` showing:
- Authentication success rate
- Failed attempts
- Potential spoofing

### Maintain Good Sender Reputation

**DO:**
- ✅ Only send from verified domains
- ✅ Keep bounce rate < 5%
- ✅ Respond to unsubscribe requests
- ✅ Use consistent FROM address

**DON'T:**
- ❌ Send bulk emails without permission
- ❌ Use misleading subject lines
- ❌ Ignore spam complaints
- ❌ Change FROM address frequently

---

## Troubleshooting Guide

### Issue: Emails not sending

**Check:**
1. SMTP credentials in config.php correct?
2. Port 587 open on server?
3. Spacemail account active?
4. Check /error_log.txt for errors

**Solution:**
```bash
# Test SMTP connection
telnet smtp.spacemail.com 587
```

### Issue: Emails going to spam

**Check:**
1. SPF record present? (dig TXT arvon.pk)
2. DKIM record present? (dig TXT spacemail._domainkey.arvon.pk)
3. DMARC record present? (dig TXT _dmarc.arvon.pk)
4. Sender reputation (use mail-tester.com)

**Solution:**
- Wait 48 hours for DNS propagation
- Verify records with DNS checker
- Request whitelist from email provider

### Issue: Reply-To not working

**Check config.php:**
```php
// Should be:
sendEmail($to, $subject, $body, true, $userEmail, $userName);
//                                     ↑ Reply-To email
```

### Issue: DNS records not propagating

**Wait Time:** 24-48 hours

**Check propagation:**
```bash
# Check SPF
dig TXT arvon.pk

# Check DKIM  
dig TXT spacemail._domainkey.arvon.pk

# Check DMARC
dig TXT _dmarc.arvon.pk
```

**Online checkers:**
- https://dnschecker.org/
- https://mxtoolbox.com/

---

## DNS Record Verification Checklist

Before going live, verify all records:

```bash
# SPF Check
nslookup -type=TXT arvon.pk
# Should show: v=spf1 include:_spf.spacemail.com ~all

# DKIM Check
nslookup -type=TXT spacemail._domainkey.arvon.pk
# Should show: v=DKIM1; k=rsa; p=...

# DMARC Check
nslookup -type=TXT _dmarc.arvon.pk
# Should show: v=DMARC1; p=quarantine...

# MX Check
nslookup -type=MX arvon.pk
# Should show: mx1.spacemail.com, mx2.spacemail.com
```

---

## Professional Email Setup (Optional)

### Connect to Gmail/Outlook

**Add info@arvon.pk to Gmail:**

1. Gmail → Settings → Accounts
2. "Add another email address"
3. SMTP Server: smtp.spacemail.com
4. Port: 587
5. Username: info@arvon.pk
6. Password: [Spacemail password]

**Now you can:**
- ✅ Send/receive from Gmail interface
- ✅ Use Gmail filters & labels
- ✅ Access on mobile
- ✅ Keep professional address

---

## Email Template (HTML)

The contact form uses this template:

```html
<h2 style='color: #8B1538;'>New Contact Form Submission</h2>
<table>
    <tr><td>Name:</td><td>John Doe</td></tr>
    <tr><td>Email:</td><td>john@email.com</td></tr>
    <tr><td>Phone:</td><td>+92 xxx xxx xxxx</td></tr>
    <tr><td>Subject:</td><td>Custom Order</td></tr>
    <tr><td>Message:</td><td>Hello, I need...</td></tr>
</table>
<p>Click 'Reply' to respond directly to the sender.</p>
```

**Customizable in:** `/api/contact.php`

---

## Security Best Practices

### Prevent Email Abuse

1. **Rate Limiting** (add to contact.php):
```php
// Max 3 emails per hour per IP
$_SESSION['email_count'] = ($_SESSION['email_count'] ?? 0) + 1;
if ($_SESSION['email_count'] > 3) {
    jsonResponse(['error' => 'Too many requests'], 429);
}
```

2. **Honeypot Field** (spam bot trap):
```html
<!-- Add hidden field in form -->
<input type="text" name="website" style="display:none">

<!-- Check in PHP -->
if (!empty($_POST['website'])) {
    die(); // Bot detected
}
```

3. **reCAPTCHA** (optional):
- Add Google reCAPTCHA v3
- Prevents automated submissions

---

## Final Verification Checklist

- [ ] Spacemail account created
- [ ] no-reply@arvon.pk created
- [ ] info@arvon.pk created
- [ ] SPF record added
- [ ] DKIM record added
- [ ] DMARC record added
- [ ] MX records added
- [ ] DNS propagated (48 hours)
- [ ] config.php updated
- [ ] Test email sent successfully
- [ ] Mail-tester.com score 10/10
- [ ] Gmail delivery verified
- [ ] Outlook delivery verified
- [ ] Yahoo delivery verified
- [ ] Reply-To working correctly
- [ ] Not landing in spam folder

---

**🎯 Expected Result:**

When someone submits contact form:
1. ✅ Email sent FROM no-reply@arvon.pk
2. ✅ Email received TO info@arvon.pk
3. ✅ Reply-To set to customer's email
4. ✅ Lands in inbox (not spam)
5. ✅ Click "Reply" goes to customer
6. ✅ Professional HTML formatting
7. ✅ No "sent to self" emails

**Email flow is now bulletproof! 📧✨**
