## Why

Customer account operations for the `PHOTOBOOKAPP` brand are currently routed through `Customise/scripts/EDL_ExternalCustomerAccount.php`, which causes inconsistent behavior and login failures such as `No group code provided` when external payloads are incomplete. We need `PHOTOBOOKAPP` to use Taopix-native account verification and account lifecycle behavior so all linked license keys behave consistently without per-key maintenance.

## What Changes

- Introduce brand-based bypass behavior for external customer account scripting when the resolved brand is `PHOTOBOOKAPP`.
- Add a dedicated `resolveGroupCode()` path so bypass decisions can also be driven from effective groupcode (license key) context.
- Support explicit legacy groupcode bypass overrides (for example `STSTEPHENS`) even when the resolved brand is not `PHOTOBOOKAPP`.
- Ensure account flows for `PHOTOBOOKAPP` are handled by Taopix core mechanisms rather than external customer API logic:
  - registration
  - login/authentication
  - password reset initiation and completion
  - password change
  - customer profile/account detail updates
- Explicitly align bypass strategy by hook contract:
  - `login`, `resetPasswordInit`, `resetPassword`, `updatePassword`: use `NOTHANDLED` so Taopix native flow continues.
  - `createAccount`, `updateAccountDetails`, `updateAccountBalance`, `updateGiftCardBalance`, `updateActiveStatus`, `deleteAccount`: bypass external API with success/no-op-compatible return values (same pattern as `updateAccountDetails`/`updateAccountBalance`), not `NOTHANDLED`.
- Define robust brand/groupcode resolution rules that do not rely on hardcoded license-key lists and support future `PHOTOBOOKAPP` license keys automatically.
- Preserve existing external-customer-account behavior for non-`PHOTOBOOKAPP` brands, except explicitly configured groupcode bypass overrides.

## Capabilities

### New Capabilities
- `photobookapp-native-customer-account-flow`: Ensure all customer account operations for `PHOTOBOOKAPP`-bound license keys bypass the external customer account script logic and execute through native Taopix account processing paths.

### Modified Capabilities
- None.

## Impact

- Affected code areas:
  - `Customise/scripts/EDL_ExternalCustomerAccount.php`
  - Taopix call sites that interpret script responses (login, register, reset password, update account details, password updates, balance sync hooks)
- Functional impact:
  - `PHOTOBOOKAPP` customer account lifecycle becomes deterministic and independent of external API response completeness (including group code propagation).
- Operational impact:
  - New license keys mapped to `PHOTOBOOKAPP` will inherit the same behavior automatically, reducing support overhead and eliminating per-license-key scripting exceptions.
