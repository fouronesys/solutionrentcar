---
name: Git push quirks
description: How to commit/push in this repl when the gitPush callback or shell git fails.
---
- Shell `git push` to GitHub always fails (auth: token not exposed to shell); shell `git commit` fails for missing identity unless run with `git -c user.name=... -c user.email=...`.
- The `gitPush` callback is no longer available in sessions (ReferenceError). The GitHub connector's proxy injects tokens server-side only — no raw token is extractable.
- Working push path: secret `GITHUB_PUSH_TOKEN` exists — `git push "https://x-access-token:${GITHUB_PUSH_TOKEN}@github.com/<owner>/<repo>" master:master` works from shell (sanitize the URL in any echoed output).
**How to apply:** commit via shell with inline identity, verify fast-forward vs origin (remote gets force-updated sometimes), then push with the token URL. Repo pack is ~665 MB (mobile/node_modules tracked) — pushes can be slow. Push to master triggers 3 GitHub Actions: Android Gradle, iOS EAS, Deploy to Hostinger (FTP del sitio PHP).
- GitHub can receive commits while local `master` is ahead; a rejected push requires fetching and merging remote history rather than force-pushing over it.
