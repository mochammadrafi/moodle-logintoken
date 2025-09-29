
# Login Token Moodle Plugin

A Moodle plugin for token-based authentication using Moodle web service tokens (wstoken), allowing users to log in via API calls.

## Features

-   API-based authentication using Moodle web service tokens
-   Support for both custom Login Token service and Moodle Mobile App service tokens
-   Simple REST endpoint for token validation and session creation
-   Session check functionality to detect if user is already logged in
-   Secure token validation with expiration checking
-   JSON response format for easy integration
-   Automatic session management

## Requirements

-   Moodle version 3.9 or higher
-   Web service tokens configured in Moodle
-   Valid wstoken for authentication

## Installation

1.  **Download the Plugin:** Download or clone the plugin from the repository
2.  **Move to Moodle Local Plugins:** Move the `logintoken` folder to the `moodle/local/` directory
3.  **Install via Moodle Interface:**
    -   Navigate to `Site administration > Notifications` in your Moodle site
    -   Moodle will detect the new plugin. Follow the on-screen instructions to complete the installation
4.  **Configure the Plugin:**
    -   After installation, go to `Site administration > Plugins > Local plugins > Login Token`
    -   Configure the plugin settings as needed

## Usage

### Web Service API

This plugin provides Moodle web service functions for token-based authentication:

#### Available Functions

1. **`local_logintoken_login_with_token`** - Login user with web service token
2. **`local_logintoken_validate_token`** - Validate web service token
3. **`local_logintoken_check_session`** - Check if user has an active session

### Supported Services

The plugin works with tokens from:
- **Login Token Service** (custom service)
- **Moodle Mobile App Service** (built-in service)

### API Endpoints

#### 1. Login with Token
```
GET /local/logintoken/auth/login.php?wstoken=YOUR_TOKEN
```

#### 2. Validate Token
```
GET /local/logintoken/auth/login.php?wstoken=YOUR_TOKEN&wsfunction=local_logintoken_validate_token
```

#### 3. Check Session
```
GET /local/logintoken/auth/check_session.php
```

### Example Usage
#### Login with Token (Mobile App Service and Custom Server)
```bash
curl "https://your-moodle-site.com/local/logintoken/auth/login.php?wstoken=YOUR_TOKEN"
```

#### Validate Token
```bash
curl "https://your-moodle-site.com/local/logintoken/auth/login.php?wstoken=YOUR_TOKEN&wsfunction=local_logintoken_validate_token"
```

#### Check Session (No Authentication Required)
```bash
curl "https://your-moodle-site.com/local/logintoken/auth/check_session.php"
```

### Response Format

#### Login Success Response:
```json
{
    "success": true,
    "message": "Login successful",
    "user": {
        "id": 123,
        "username": "john.doe",
        "firstname": "John",
        "lastname": "Doe",
        "email": "john.doe@example.com",
        "fullname": "John Doe"
    },
    "redirect_url": "https://your-moodle-site.com/",
    "session_id": "abc123def456"
}
```

#### Token Validation Response:
```json
{
    "valid": true,
    "message": "Token is valid",
    "user": {
        "id": 123,
        "username": "john.doe",
        "firstname": "John",
        "lastname": "Doe",
        "email": "john.doe@example.com",
        "fullname": "John Doe"
    },
    "token_info": {
        "created": 1701234567,
        "last_access": 1701234567,
        "valid_until": 1701320967
    }
}
```

#### Session Check Response (User Logged In):
```json
{
    "logged_in": true,
    "message": "User is logged in",
    "user": {
        "id": 123,
        "username": "john.doe",
        "firstname": "John",
        "lastname": "Doe",
        "email": "john.doe@example.com",
        "fullname": "John Doe"
    },
    "session_info": {
        "session_id": "abc123def456",
        "session_start": 1701234567,
        "last_activity": 1701234567,
        "user_agent": "Mozilla/5.0...",
        "ip_address": "192.168.1.100"
    }
}
```

#### Session Check Response (User Not Logged In):
```json
{
    "logged_in": false,
    "message": "No active session found",
    "user": null,
    "session_info": {
        "session_id": "abc123def456",
        "session_start": null,
        "last_activity": null
    }
}
```

#### Error Response:
```json
{
    "success": false,
    "error": "Invalid token",
    "message": "The provided wstoken is invalid or expired"
}
```

## Check Session Use Cases

The Check Session functionality is useful for:

- **Pre-login validation**: Check if user is already logged in before showing login form
- **Session monitoring**: Monitor active user sessions in your application
- **User status verification**: Verify if user account is active and not suspended
- **Session information**: Get detailed session data including IP address and user agent
- **Authentication state**: Determine authentication status without requiring tokens

### Example JavaScript Integration

```javascript
// Check if user is already logged in
fetch('/local/logintoken/auth/check_session.php')
  .then(response => response.json())
  .then(data => {
    if (data.logged_in) {
      console.log('User is logged in:', data.user.username);
      // Redirect to dashboard or show user menu
      window.location.href = '/dashboard';
    } else {
      console.log('User not logged in');
      // Show login form
      showLoginForm();
    }
  });
```

## Development

### Code Structure

-   `auth/login.php`: Main API endpoint for token-based authentication
-   `auth/check_session.php`: Session check endpoint (no authentication required)
-   `settings.php`: Defines the plugin settings for configuration
-   `lib.php`: Contains the core logic for login page handling
-   `lang/en/local_login.php`: Contains language strings for the plugin

### Security Features

-   Token validation against Moodle's external_tokens table
-   Token expiration checking
-   User status validation (not deleted or suspended)
-   Session state checking without authentication
-   HTTPS requirement option
-   Session management

## Contributing

1.  Fork the repository
2.  Create a new branch (`git checkout -b feature/your-feature`)
3.  Commit your changes (`git commit -m 'Add some feature'`)
4.  Push to the branch (`git push origin feature/your-feature`)
5.  Open a pull request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
