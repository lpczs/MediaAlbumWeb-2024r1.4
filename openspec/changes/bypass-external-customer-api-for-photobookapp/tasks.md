## 1. Brand Resolution Foundation

- [x] 1.1 Add a shared helper in `Customise/scripts/EDL_ExternalCustomerAccount.php` to resolve effective brand from `brandcode/newbrandcode/origbrandcode`.
- [x] 1.2 Extend the helper to resolve brand via license-key lookup from available groupcode fields (`groupcode`, `accountgroupcode`, `newgroupcode`, `origgroupcode`) when explicit brand is missing.
- [x] 1.3 Add final fallback resolution from session web brand context and default to existing behavior when brand remains unresolved.
- [x] 1.4 Add a shared helper/guard that returns whether PHOTOBOOKAPP bypass should apply for the current hook invocation.

## 2. NOTHANDLED-Compatible Hook Bypass

- [x] 2.1 Update `login` to return `NOTHANDLED` for PHOTOBOOKAPP and skip all external customer API calls.
- [x] 2.2 Update `resetPasswordInit` to return `NOTHANDLED` for PHOTOBOOKAPP and skip external redirect flow.
- [x] 2.3 Update `resetPassword` to return `NOTHANDLED` for PHOTOBOOKAPP and skip external password reset operations.
- [x] 2.4 Update `updatePassword` to return `NOTHANDLED` for PHOTOBOOKAPP and skip external password update operations.

## 3. Success-Expected Hook Bypass

- [x] 3.1 Update `createAccount` so PHOTOBOOKAPP returns a successful local-flow-compatible payload without invoking external customer creation.
- [x] 3.2 Update `updateAccountDetails` so PHOTOBOOKAPP returns success (`''`) without invoking external customer update APIs.
- [x] 3.3 Update `updateAccountBalance` and `updateGiftCardBalance` so PHOTOBOOKAPP returns successful no-op-compatible results without external sync calls.
- [x] 3.4 Update `updateActiveStatus` and `deleteAccount` so PHOTOBOOKAPP returns successful no-op-compatible results without external API calls.

## 4. Validation and Regression Safety

- [x] 4.1 Validate PHOTOBOOKAPP flows end-to-end: register, login, forgot/reset password, change password, and profile update.
- [x] 4.2 Validate that PHOTOBOOKAPP-linked groupcodes (including newly mapped keys) are covered without hardcoded license-key allowlists.
- [x] 4.3 Run spot regression checks for at least one non-PHOTOBOOKAPP brand to confirm existing external-customer-account behavior is unchanged.
- [x] 4.4 Verify no `No group code provided` error is emitted for PHOTOBOOKAPP login paths after bypass behavior is active.
