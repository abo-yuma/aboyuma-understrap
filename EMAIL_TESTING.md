# Testing Contact Form Emails Locally

This guide explains how to safely test the WordPress contact form on your local machine without sending real emails.

---

## **1. Install MailHog**

MailHog is a local SMTP server that captures emails and shows them in a browser interface.

1. Download MailHog for Windows:  
   [MailHog Releases](https://github.com/mailhog/MailHog/releases)

   - Choose `MailHog_windows_amd64.exe` and rename to `MailHog.exe`.

2. Run MailHog:  
   Open a terminal in the folder where `MailHog.exe` is located and run:

   ```cmd
   MailHog.exe

   ```

3. Confirm it’s running. You should see:

   ```
   SMTP: 0.0.0.0:1025
   Web UI: http://0.0.0.0:8025
   ```

---

## **2. Configure PHP / WordPress to use MailHog**

### Option A — Edit `php.ini` (simplest)

1. Open your PHP configuration file (`php.ini`).

2. Set the mail function to use MailHog:

   ```ini
   [mail function]
   SMTP = localhost
   smtp_port = 1025
   sendmail_from = test@example.com
   ```

3. Save the file and restart your local web server (XAMPP, WAMP, MAMP, etc.).

---

### Option B — Use WP Mail SMTP plugin

1. Install [WP Mail SMTP](https://wordpress.org/plugins/wp-mail-smtp/) in WordPress.
2. Configure **Other SMTP**:

   - **SMTP Host:** `localhost`
   - **SMTP Port:** `1025`
   - **Encryption:** None
   - **Authentication:** Off

3. Save settings.

---

## **3. Submit a test email**

1. Open your WordPress contact page.
2. Fill in the form:

   - **Email:** any valid email (e.g., `you@example.com`)
   - **Title:** test subject
   - **Notes:** test message
   - **Attachment:** optional, must be `.jpg`, `.png`, or `.pdf` ≤ 2MB

3. Click **“📩 ส่งข้อมูล”** to submit.

---

## **4. View the email in MailHog**

1. Open your browser to `http://localhost:8025`.
2. You should see a new email with:

   - **To:** your WordPress admin email (`get_option('admin_email')`)
   - **Subject:** `New Contact: <your title>`
   - **Body:** includes sender email, title, and notes
   - **Attachments:** if uploaded

> Emails are **not sent to real users** — they are captured by MailHog.

---

## **5. Debugging Tips**

- If the email does not appear:

  1. Make sure MailHog is running and listening on port `1025`.
  2. Make sure PHP / WP is configured to send emails to `localhost:1025`.
  3. Enable WordPress debug logs:

     ```php
     error_log("Testing wp_mail: to $to, subject $subject");
     ```

- You can also run MailHog in the background and redirect logs to a file:

  ```cmd
  MailHog.exe > mailhog.log 2>&1
  ```

---

## **6. Notes**

- **Safe testing:** MailHog ensures no emails leave your local machine.
- **Attachments and HTML:** MailHog supports file attachments and HTML emails.
- **Production:** On a production server, `wp_mail()` should use your real SMTP/email server.

---
