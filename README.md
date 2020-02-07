# Generating PHP code from proto files
## Prerequisites MAC
```
brew install protobuf
wget https://github.com/spiral/php-grpc/releases/download/v1.2.0/protoc-gen-php-grpc-1.2.0-darwin-amd64.zip
unzip protoc-gen-php-grpc-1.2.0-darwin-amd64.zip  
sudo mv protoc-gen-php-grpc-1.2.0-darwin-amd64/protoc-gen-php-grpc /usr/local/bin/
rm -fR protoc-gen-php-grpc-1.2.0-darwin-amd64
rm protoc-gen-php-grpc-1.2.0-darwin-amd64.zip
```

## Prerequisites LINUX
1. Code for different languages is generated using a the "Protocol Compiler" or "protoc".
You can use the following script to automatically download and install it.
```shell script
get_latest_release() {
  # $1 repository name, e. g. foobar-org/foobar
  # $2 regexp matching package name
  curl --silent "https://api.github.com/repos/$1/releases/latest" | # Get latest release from GitHub api
    grep "\"browser_download_url\":.*/$2" |                                            # Get tag line
    sed -E 's/.*"([^"]+)".*/\1/'                                    # Pluck JSON value
}

# Instll proto compiler (protoc). Out of the box it can only compile PHP models and PHP clients
PROTOC_ZIP=protoc.zip
curl -L "$(get_latest_release protocolbuffers/protobuf protoc-.*-linux-x86_64.zip)" -o $PROTOC_ZIP
sudo unzip -o $PROTOC_ZIP -d /usr/local bin/protoc
sudo unzip -o $PROTOC_ZIP -d /usr/local 'include/*'
rm -f $PROTOC_ZIP
```
Or download and install `protoc` manually from https://github.com/protocolbuffers/protobuf/releases
2. `protoc` cannot generate server stubs out of the box.
For that we are going to use `protoc-gen-php-grpc` plugin from Spiral. It will provide an extra option
`--php-grpc_out` for `protoc` which is used to specify path for gRPC server stubs output.
Use the command below to install it on Linux.
```shell script
mkdir protoc-gen-php-grpc
curl -sSL "$(get_latest_release spiral/php-grpc protoc-gen-php-grpc-.*-linux-amd64.tar.gz)" | \
  tar -xvzf - -C protoc-gen-php-grpc --strip-components 1
sudo mv protoc-gen-php-grpc/protoc-gen-php-grpc /usr/local/bin/
rm -r protoc-gen-php-grpc
```
See https://github.com/spiral/php-grpc/releases for a full list of versions. 
## Generating code
Once you've got `protoc` with the plugin installed, you can generate PHP code.
In general it's done like this: 
```
protoc --php_out=Contract/ --php-grpc_out=Contract/ -I path/to/import/root/dir path/to/service/dir/**/*.proto
```
`protoc` will create folder structure according to PHP namespaces configured in proto files.
Since App namespace is usually mapped to `src` directory, the resulting folder structure may be not entirely correct.
For convenience `bin/generate-contract.sh` script is provided. It assumes that these conventions are always followed:
- All generated files are within `App\Contract` namespace
- `src` dir is mapped to `App` namespace
- Output location is `src/Contract`
Usage:
```shell script
bin/generate-contract.sh path/to/import/dir path/to/protos/to/compile.proto
# Note that the arguments can use glob patterns to specify multiple import dirs
# and to compile multiple proto files at once.
# Arguments containing glob patterns must be quoted, glob expansion will be
# performed inside the generate-contract.sh script itself
# Example:
bin/generate-contract.sh 'import/path/*' 'path/to/protos/**/*.proto'
```
