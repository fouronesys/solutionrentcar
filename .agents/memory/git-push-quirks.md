---
name: Git push quirks
description: How to commit/push in this repl when the gitPush callback or shell git fails.
---
- Shell `git push` to GitHub always fails (auth: token not exposed to shell); shell `git commit` fails for missing identity unless run with `git -c user.name=... -c user.email=...`.
- The `gitPush` callback is no longer available in sessions (ReferenceError). The GitHub connector's proxy injects tokens server-side only — no raw token is extractable (`client.auth()` returns no token), so shell `git push` cannot be authenticated by the agent at all.
**How to apply:** commit locally via shell with inline identity so work is safe, then tell the user to push from the Git pane. Repo pack is ~665 MB (mobile/node_modules is tracked in git) — pushes are slow/timeout-prone. Local branch is `master`; remote default is `origin/master` (origin also has `main`).
