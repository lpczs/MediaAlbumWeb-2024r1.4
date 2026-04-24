## Context

`Customise/scripts/EDL_ExternalCustomerAccount.php` currently implements external customer-account integration for multiple account lifecycle paths (register, login, reset-password init/complete, profile update, password update, and account balance sync hooks). For `PHOTOBOOKAPP`, this creates instability because customer authentication and account state are coupled to external payload quality (for example, missing `groupcode` from external login responses).

The system already supports a fallback model where script methods can return `NOTHANDLED` and Taopix native logic continues. However, not all call sites interpret `NOTHANDLED` identically: some paths explicitly treat it as “continue internally,” while others expect success/empty result and would treat `NOTHANDLED` as failure.

The change must therefore bypass external API behavior for all `PHOTOBOOKAPP`-bound license keys without maintaining explicit per-license-key lists, while preserving existing behavior for all other brands.

## Goals / Non-Goals

**Goals:**
- Ensure `PHOTOBOOKAPP` customer flows are executed by Taopix native account mechanisms rather than external customer API logic.
- Apply bypass behavior to all current and future license keys mapped to `PHOTOBOOKAPP`.
- Preserve existing external integration behavior for non-`PHOTOBOOKAPP` brands.
- Make bypass decisions deterministic across hook methods with a single brand-resolution rule.

**Non-Goals:**
- Replacing or removing the external customer-account integration for other brands.
- Refactoring Taopix core authentication architecture.
- Changing data schema or introducing new persistence tables.
- Introducing admin UI controls for per-brand bypass toggles in this change.

## Decisions

### 1. Centralize brand resolution inside `EDL_ExternalCustomerAccount.php`

**Decision:** Add a shared helper to resolve effective brand for each hook invocation using ordered fallbacks:
1. Direct brand params if present (`brandcode`, `newbrandcode`, `origbrandcode`)
2. Group-code derived brand using Taopix lookup (`DatabaseObj::getLicenseKeyFromCode(...)['webbrandcode']`) from available group fields (`groupcode`, `accountgroupcode`, `newgroupcode`, `origgroupcode`)
3. Session fallback (`$gSession['webbrandcode']`) when available

**Rationale:**
- Not all hooks are guaranteed to receive `brandcode` consistently.
- Group-to-brand mapping is authoritative and automatically covers future license keys.
- Keeps bypass logic independent of hardcoded key lists like `KAN-PHOTO`.

**Alternatives considered:**
- Hardcode `KAN-PHOTO` (and similar) in script logic: rejected due to maintenance risk and inability to auto-cover future keys.
- Depend only on `$gSession['webbrandcode']`: rejected because some background/system-triggered hooks may not have reliable session context.

### 2. Use method-specific bypass semantics (not one universal return)

**Decision:** For resolved brand `PHOTOBOOKAPP`, bypass external API calls with response behavior aligned to each Taopix caller contract.

**For hooks where Taopix supports `NOTHANDLED` continuation:**
- `login`
- `resetPasswordInit`
- `resetPassword`
- `updatePassword`

Return `NOTHANDLED` so Taopix-native path executes.

**For hooks where caller expects success (`''` or success array) and may treat other values as failure:**
- `createAccount`: return success result array (empty `result`, pass-through accountcode/useraccount fields as required)
- `updateAccountDetails`: return `''`
- `updateAccountBalance`: no-op and return success (`''`/equivalent)
- `updateGiftCardBalance`: no-op and return success (`''`/equivalent)
- `updateActiveStatus`: no-op and return success (`''`/equivalent)
- `deleteAccount`: no-op and return success (`''`/equivalent)

**Rationale:**
- A single bypass token across all methods is unsafe because call-site contracts differ.
- Preserves stable UX and avoids false failures in account/profile workflows.

**Alternatives considered:**
- Return `NOTHANDLED` from every method: rejected because some paths do not interpret it as continue.
- Keep external calls but add defensive defaults: rejected because requirement is to fully use Taopix native account verification for this brand.

### 3. Keep SSO-specific behavior unchanged unless explicitly in scope

**Decision:** Do not broaden SSO architecture changes beyond this brand-bypass behavior for customer account API hooks.

**Rationale:**
- Requirement targets customer account API bypass for `PHOTOBOOKAPP` lifecycle flows.
- Minimizes regression surface in existing SSO integrations for other brands.

**Alternatives considered:**
- Rework `ssoLogin` and full SSO pipeline now: deferred to separate change if required.

## Risks / Trade-offs

- **[Risk] Inconsistent return contracts across hooks can cause regressions if bypass return values are wrong**
  → **Mitigation:** Define explicit per-method return behavior and verify each call path with targeted tests/checklists.

- **[Risk] Brand resolution may fail in edge contexts with sparse params and no session**
  → **Mitigation:** Prefer groupcode-to-brand lookup before session fallback; if unresolved, preserve existing non-bypass behavior and log debug context.

- **[Risk] No-op external sync hooks may diverge external system balances/states for `PHOTOBOOKAPP`**
  → **Mitigation:** Accept as intended trade-off for this brand; document operational expectation that Taopix is source of truth.

- **[Risk] Hidden dependencies on external account IDs/accountcode updates during registration**
  → **Mitigation:** For bypassed registration, return successful local-flow-compatible payload; validate account creation/login/reset/profile end-to-end for `PHOTOBOOKAPP`.

## Migration Plan

1. Implement helper-based brand resolution and `PHOTOBOOKAPP` bypass guard in `EDL_ExternalCustomerAccount.php`.
2. Apply method-specific bypass return semantics for all affected hooks.
3. Validate flows with manual test matrix for `PHOTOBOOKAPP`:
   - register
   - login (username/email)
   - forgot/reset password
   - change password in account area
   - update profile details
4. Run regression spot checks for a non-`PHOTOBOOKAPP` brand to confirm existing external behavior is unchanged.
5. Deploy as normal application release.

**Rollback strategy:**
- Revert the script changes in `EDL_ExternalCustomerAccount.php` to restore prior external API integration behavior immediately.

## Open Questions

- Should bypass/no-op behavior also include any custom hooks not currently active in this script (for example, commented or optional methods) to guarantee future consistency?
- Do we need explicit runtime logging for bypass decisions (`brand`, `method`, `decision`) in production, or only temporary debug logging during rollout?
