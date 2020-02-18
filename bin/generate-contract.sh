#!/usr/bin/env bash
set -e

timestamp=$(date '+%Y%m%d%H%M%S')
tmpdir="/tmp/${timestamp}"

mkdir -p "$tmpdir"

mapfile -t files_to_compile < <(find "$2" -type f -name "*.proto")

protoc \
  --php_out="$tmpdir" \
  --php-grpc_out="$tmpdir" \
  -I "$1" \
  "${files_to_compile[@]}"

rm -rf src/Contract
mv "${tmpdir}/App/Contract" src/
rm -rf "$tmpdir"
