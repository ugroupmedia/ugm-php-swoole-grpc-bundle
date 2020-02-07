#!/usr/bin/env bash
set -e

timestamp=$(date '+%Y%m%d%H%M%S')
tmpdir="/tmp/${timestamp}"

mkdir -p "$tmpdir"
shopt -s globstar

# Perform glob expansion explicitly
# shellcheck disable=SC2206
import_dirs=($1)
import_dirs_args=()
for file in "${import_dirs[@]}"; do
  import_dirs_args=( "${import_dirs_args[@]}" "-I" "$file" )
done

# Perform glob expansion explicitly
# shellcheck disable=SC2206
files_to_compile=($2)
protoc \
  --php_out="$tmpdir" \
  --php-grpc_out="$tmpdir" \
  "${import_dirs_args[@]}" \
  "${files_to_compile[@]}"

rm -rf src/Contracts
mv "${tmpdir}/App/Contracts" src/
rm -rf "$tmpdir"
