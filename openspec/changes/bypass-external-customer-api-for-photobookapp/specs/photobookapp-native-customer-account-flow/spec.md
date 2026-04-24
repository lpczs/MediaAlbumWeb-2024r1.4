## ADDED Requirements

### Requirement: Brand-based bypass for PHOTOBOOKAPP customer account flows
The system MUST bypass external customer account API handling when the effective brand for a customer-account scripting hook is `PHOTOBOOKAPP`.

#### Scenario: Bypass is enabled for PHOTOBOOKAPP
- **WHEN** a customer account scripting hook is invoked and the effective brand resolves to `PHOTOBOOKAPP`
- **THEN** the hook processing MUST follow PHOTOBOOKAPP bypass behavior and MUST NOT call the external customer API endpoint

#### Scenario: Bypass is not enabled for other brands
- **WHEN** a customer account scripting hook is invoked and the effective brand resolves to a brand other than `PHOTOBOOKAPP`
- **THEN** existing external customer account API behavior MUST remain unchanged

### Requirement: Effective brand resolution without license-key allowlists
The system MUST determine the effective brand for a hook invocation without relying on hardcoded license-key lists.

#### Scenario: Resolve brand from explicit brand fields
- **WHEN** hook parameters include a brand field (`brandcode`, `newbrandcode`, or `origbrandcode`)
- **THEN** the system MUST use that brand value as the effective brand

#### Scenario: Resolve brand from groupcode mapping
- **WHEN** explicit brand fields are unavailable and a groupcode field is present (`groupcode`, `accountgroupcode`, `newgroupcode`, or `origgroupcode`)
- **THEN** the system MUST resolve the effective brand via license-key-to-brand lookup

#### Scenario: Fallback resolution from session context
- **WHEN** neither explicit brand fields nor groupcode-derived brand can be resolved
- **THEN** the system MUST attempt to resolve the effective brand from session web brand context before applying non-bypass behavior

### Requirement: Bypass contract for NOTHANDLED-compatible hooks
For PHOTOBOOKAPP, hooks that support Taopix native continuation via `NOTHANDLED` MUST return `NOTHANDLED`.

#### Scenario: Login uses Taopix-native verification
- **WHEN** `login` is invoked for PHOTOBOOKAPP
- **THEN** the hook MUST return `NOTHANDLED` so Taopix native login verification executes

#### Scenario: Password reset flow uses Taopix-native verification
- **WHEN** `resetPasswordInit` or `resetPassword` is invoked for PHOTOBOOKAPP
- **THEN** the hook MUST return `NOTHANDLED` so Taopix native reset-password processing executes

#### Scenario: Password update uses Taopix-native verification
- **WHEN** `updatePassword` is invoked for PHOTOBOOKAPP
- **THEN** the hook MUST return `NOTHANDLED` so Taopix native password update processing executes

### Requirement: Bypass contract for success-expected hooks
For PHOTOBOOKAPP, hooks whose callers expect success responses MUST return successful no-op-compatible responses and MUST NOT call external APIs.

#### Scenario: Registration path remains successful without external API
- **WHEN** `createAccount` is invoked for PHOTOBOOKAPP
- **THEN** the hook MUST return a successful result payload compatible with Taopix local account creation flow

#### Scenario: Profile updates remain successful without external API
- **WHEN** `updateAccountDetails` is invoked for PHOTOBOOKAPP
- **THEN** the hook MUST return success (`''`) and MUST allow Taopix local profile updates to continue

#### Scenario: Account state sync hooks are safe no-ops for PHOTOBOOKAPP
- **WHEN** `updateAccountBalance`, `updateGiftCardBalance`, `updateActiveStatus`, or `deleteAccount` is invoked for PHOTOBOOKAPP
- **THEN** each hook MUST return a successful no-op-compatible result and MUST NOT call external customer API operations
