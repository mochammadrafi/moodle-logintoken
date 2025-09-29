# Installation Guide

## Quick Setup

1. **Copy the plugin files to your Moodle installation:**
   ```bash
   cp -r logintoken /path/to/moodle/local/
   ```

2. **Install the plugin:**
   - Go to `Site administration > Notifications` in your Moodle site
   - Follow the installation prompts

3. **Configure the plugin:**
   - Go to `Site administration > Plugins > Local plugins > Login Token`
   - Configure the settings as needed

## Web Service Setup

### 1. Enable Web Services
- Go to `Site administration > Advanced features`
- Enable "Web services"
- Enable "Enable web services"

### 2. Create External Service
- Go to `Site administration > Plugins > Web services > External services`
- Click "Add" to create a new service
- Name: "Login Token Service"
- Short name: "logintoken"
- Enable the service

### 3. Add Functions to Service
- In the external service, add these functions:
  - `local_logintoken_login_with_token`
  - `local_logintoken_validate_token`

### 4. Create Web Service User
- Go to `Site administration > Users > Add a new user`
- Create a user specifically for web service access
- Set authentication method to "Web service authentication"

### 5. Generate Token
- Go to `Site administration > Plugins > Web services > Manage tokens`
- Click "Add" to create a new token
- Select the web service user and external service
- Save the token (this is your `wstoken`)

## API Usage

### Web Service Functions

#### 1. Login with Token
```bash
curl "https://your-moodle-site.com/auth/login.php?wstoken=YOUR_TOKEN"
```

#### 2. Validate Token
```bash
curl "https://your-moodle-site.com/auth/login.php?wstoken=YOUR_TOKEN&wsfunction=local_logintoken_validate_token"
```

### JavaScript Example
```javascript
// Login with token
fetch('/auth/login.php?wstoken=YOUR_TOKEN')
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      console.log('Login successful:', data.user);
      window.location.href = data.redirect_url;
    } else {
      console.error('Login failed:', data.message);
    }
  });

// Validate token
fetch('/auth/login.php?wstoken=YOUR_TOKEN&wsfunction=local_logintoken_validate_token')
  .then(response => response.json())
  .then(data => {
    if (data.valid) {
      console.log('Token is valid for user:', data.user);
    } else {
      console.error('Token validation failed:', data.message);
    }
  });
```

### PHP Example
```php
// Login with token
$token = 'your_wstoken_here';
$url = 'https://your-moodle-site.com/auth/login.php?wstoken=' . urlencode($token);

$response = file_get_contents($url);
$data = json_decode($response, true);

if ($data['success']) {
    echo "Login successful for user: " . $data['user']['username'];
    echo "Redirect to: " . $data['redirect_url'];
} else {
    echo "Login failed: " . $data['message'];
}

// Validate token
$validate_url = 'https://your-moodle-site.com/auth/login.php?wstoken=' . urlencode($token) . '&wsfunction=local_logintoken_validate_token';
$validate_response = file_get_contents($validate_url);
$validate_data = json_decode($validate_response, true);

if ($validate_data['valid']) {
    echo "Token is valid for user: " . $validate_data['user']['username'];
} else {
    echo "Token validation failed: " . $validate_data['message'];
}
```

## Security Notes

- Always use HTTPS in production
- Keep your wstoken secure and don't expose it in client-side code
- Regularly rotate your web service tokens
- Monitor API usage for suspicious activity
- Use proper user permissions for web service users

## Troubleshooting

### Common Issues

1. **"Invalid token" error:**
   - Check that the wstoken is correct
   - Verify the token hasn't expired
   - Ensure the user associated with the token is active

2. **"User not found" error:**
   - The user associated with the token may be deleted or suspended
   - Check user status in Moodle admin

3. **"Token expired" error:**
   - The token has passed its expiration date
   - Generate a new token in Moodle admin

4. **"Web service error":**
   - Check that the web service is properly configured
   - Verify the external service is enabled
   - Ensure the functions are added to the service

### Debug Mode

To enable debug logging, add this to your config.php:
```php
$CFG->debug = E_ALL;
$CFG->debugdisplay = 1;
```
