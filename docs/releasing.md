# Publishing a version

A GitHub release records the source version. A container registry stores the
deployable image. Publishing a GitHub release does not automatically build or
publish a Docker image; there is currently no release-build workflow in this repo.

Use the same numeric version for both, for example Git tag `v1.0.3` and image
`your-dockerhub-user/studyorganizer:1.0.3`. All names below are placeholders.

## 1. Prepare a release commit

Update `CHANGELOG.md`, the versioned notes under `docs/releases/`, and the
production template's example image tag. Run the tests and inspect the changes:

```bash
npm run build
php artisan test --filter='CreateAdminCommandTest|AdminPanelAccessTest|ProxyHeadersTest|HttpBootstrapTest'
git diff --check
git status --short
```

Stage only the source, configuration templates, tests, and docs intended for
the release. Review `git diff --cached` before committing. Environment templates
must have blank secrets and example domains; never stage real `.env` files,
database dumps, uploads, private keys, or diagnostic outputs. The repository's
ignore rules help keep these files out of Git.

Commit the reviewed changes, then verify the release version does not already
exist on GitHub. Use a new version for each release rather than replacing a tag.

## 2. Build and publish the image

From that source revision, log in to the container registry:

```bash
docker login
```

For an Intel/AMD Linux VM, a normal two-step build is sufficient:

```bash
docker build --platform linux/amd64 --target runtime \
  -t your-dockerhub-user/studyorganizer:1.0.3 .
docker push your-dockerhub-user/studyorganizer:1.0.3
```

The equivalent one-step build/publish command is:

```bash
docker buildx build --platform linux/amd64 --target runtime \
  -t your-dockerhub-user/studyorganizer:1.0.3 --push .
```

`buildx` is Docker's build tooling. Both commands above read the `Dockerfile`
directly; no Compose file is involved. The final `.` supplies the checkout as
the build context, `runtime` selects the production stage, and the tag names
the resulting image. Composer dependencies, Vite assets, and Filament assets
are assembled by earlier stages.

Check the VM architecture with `uname -m`: `x86_64` requires `linux/amd64` and
`aarch64` requires `linux/arm64`. Building on an ARM laptop for an Intel/AMD VM
still needs the explicit `linux/amd64` platform.

For both architectures, use a builder that supports multi-platform output:

```bash
# Create once; later select it with docker buildx use studyorganizer-builder.
docker buildx create --name studyorganizer-builder \
  --driver docker-container --use --bootstrap
docker buildx build --platform linux/amd64,linux/arm64 --target runtime \
  -t your-dockerhub-user/studyorganizer:1.0.3 --push .
```

The builder is a build tool, not an application service deployed on the VM.
There is one StudyOrganizer image: web, migration, queue, and scheduler
containers reuse it with different commands. PostgreSQL uses its official image.

Verify the registry result:

```bash
docker buildx imagetools inspect your-dockerhub-user/studyorganizer:1.0.3
```

Use private registry access if appropriate for your project. A pushed image
contains application source and dependencies even when runtime secrets are
excluded. Registry tokens should be entered through the login flow, not stored
in repository files or release notes.

## 3. Tag and publish on GitHub

After committing, pushing the reviewed source, and authenticating the GitHub CLI:

```bash
git tag -a v1.0.3 -m "StudyOrganizer v1.0.3"
git push origin main
git push origin v1.0.3
gh release create v1.0.3 --verify-tag \
  --title "StudyOrganizer v1.0.3" \
  --notes-file docs/releases/v1.0.3.md
```

The `--verify-tag` flag ensures the release points to the tag you pushed rather
than implicitly tagging a different revision. Do not upload environment files
or database backups as release assets. GitHub provides source archives from
the tagged commit; include only intended repository content in that commit.

## 4. Deploy the published image

On the VM, pull the source/configuration update and preserve its existing
`.env.production`. Set:

```dotenv
APP_IMAGE=your-dockerhub-user/studyorganizer:1.0.3
```

Use `prod` plus `native` for host-installed Nginx, or `prod` plus `proxy` for
Docker Nginx. Omit `docker-compose.build.yml` when using the published image.
Follow the production guide's fresh-install or maintenance-update sequence;
the latter includes backups, stopping background writes, migrations, and
recreating application services. Keep existing keys, passwords, and volumes.

Ensure all enabled application services use the same release image. A `git pull`
on the VM updates deployment files; changing PHP source inside an image requires
pulling and recreating from a newly built image. Verify `/up`, HTTPS login,
admin access, and upload/download behavior after deployment.
