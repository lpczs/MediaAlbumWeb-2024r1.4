## Agent Git Safety

- Prefer editing files only. Do not perform irreversible Git operations unless explicitly asked.
- It is acceptable to inspect Git state with read-only commands such as `git status`, `git diff`, `git log`, and `git branch`.
- Ask before running commands that publish, rewrite, deploy, or release anything.
- When in doubt, explain the proposed Git operation and wait for explicit approval.

## Git Workflow

This repository follows trunk-based development with short-lived feature branches and a protected `main` branch.

### Branching

- Do not commit directly to `main` or `master`.
- Before making code changes, check the current branch with `git status -sb`.
- If the current branch is `main` or `master`, create or ask for a short-lived feature branch before editing files.
- Use focused branch names:
  - `feat/<short-description>` for new features
  - `fix/<short-description>` for bug fixes
  - `chore/<short-description>` for maintenance work
  - `docs/<short-description>` for documentation-only changes
  - `test/<short-description>` for test-only changes
- Keep branches small and focused on one logical change.

### Commits

- Use Conventional Commits for commit messages:
  - `feat: ...`
  - `fix: ...`
  - `chore: ...`
  - `docs: ...`
  - `test: ...`
  - `refactor: ...`
  - `build: ...`
  - `ci: ...`
- Keep commits focused and avoid mixing unrelated changes.
- Do not amend commits, rebase, squash, force-push, or rewrite history unless explicitly asked.

### Pull Requests and Checks

Before considering work complete, run the relevant checks for the changed areas when available:

- lint
- tests
- build
- OpenAPI contract validation
- container build

If a check cannot be run in the current environment, clearly report:

- which check was not run
- why it was not run
- what risk remains

Do not mark work as complete if known lint, test, build, contract, or container errors remain unexplained.

### Service Versioning and Tags

- Services use semantic version tags in the format `<service>-vX.Y.Z`, for example:
  - `frontend-v1.2.3`
  - `api-v2.0.0`
- Use semantic versioning rules:
  - breaking change: major version
  - backward-compatible feature: minor version
  - bug fix: patch version
- Do not create release tags or bump service versions unless explicitly asked.
- When discussing a release, identify which service or services are affected.

### Releases and Docker Images

- Releases use environment-specific configuration through `.env` files and secret management.
- Never commit real secrets, credentials, tokens, private keys, or production `.env` files.
- If a new environment variable is required, update the appropriate example file or documentation, such as `.env.example`, without exposing real secret values.
- Docker images should be tagged with both:
  - the git SHA for traceability
  - the service semantic version tag for release readability
- Do not push Docker images unless explicitly asked.

### Protected Operations

Do not perform any of the following unless explicitly requested:

- push to a remote branch
- push to `main` or `master`
- create or merge a pull request
- create, delete, or move git tags
- bump release versions
- publish packages or Docker images
- modify production secrets or deployment configuration
