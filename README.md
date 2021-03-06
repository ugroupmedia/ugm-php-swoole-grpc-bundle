# Generating PHP code from proto files
## 1. Install protoc

Code for different languages is generated using a the "Protocol Buffer Compiler" or "protoc".
You can use the following script to automatically download and install it.

**On MacOS**
```
brew install protobuf
```
**On Linux**
```shell script
get_latest_version() {
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

## 2. Install `protoc-gen-php-grpc` plugin
`protoc` cannot generate server stubs out of the box.
For that we are going to use `protoc-gen-php-grpc` plugin from Spiral. It will provide an extra option
`--php-grpc_out` for `protoc` which is used to specify path for gRPC server stubs output.

### 2.1. Default installation
You can install an official pre-built binary from Spiral. It will allow you to generate interfaces for your
gRPC services defined in proto files. You'll get one PHP interface per each gRPC service.
If you wish to have individual an interface for each rpc method, skip to 2.2.

**On MacOS**
```
wget https://github.com/spiral/php-grpc/releases/download/v1.2.0/protoc-gen-php-grpc-1.2.0-darwin-amd64.zip
unzip protoc-gen-php-grpc-1.2.0-darwin-amd64.zip  
sudo mv protoc-gen-php-grpc-1.2.0-darwin-amd64/protoc-gen-php-grpc /usr/local/bin/
rm -fR protoc-gen-php-grpc-1.2.0-darwin-amd64
rm protoc-gen-php-grpc-1.2.0-darwin-amd64.zip
```
**On Linux**
```shell script
mkdir protoc-gen-php-grpc
curl -sSL "$(get_latest_release spiral/php-grpc protoc-gen-php-grpc-.*-linux-amd64.tar.gz)" | \
  tar -xvzf - -C protoc-gen-php-grpc --strip-components 1
sudo mv protoc-gen-php-grpc/protoc-gen-php-grpc /usr/local/bin/
rm -r protoc-gen-php-grpc
```

### 2.2. Patched `protoc-gen-php-grpc` plugin
If you wish to separate concerns further or you're trying to implement ADR pattern, you can generate an interface per
each rpc method.
You'll need to patch plugin's source and build it from source. Make sure you have Go-lang installed and configured
before executing the following commands.
```
old_dir="$(pwd)"
build_dir=/tmp/php-grpc
rm -rf ${build_dir}
# Clone the repo 
git clone https://github.com/spiral/php-grpc "${build_dir}"
cd "${build_dir}"

# Checkout the latest tag
git fetch --tags
latestTag=$(git describe --tags $(git rev-list --tags --max-count=1))
git checkout "$latestTag"

# Apply the patch
curl https://github.com/supersmile2009/php-grpc/commit/dab727e8a5fe96a46378a0f459ce4d7893ab8aae.patch | git apply

# Build and install the plugin
cd "cmd/protoc-gen-php-grpc"
go get
go build
sudo mv protoc-gen-php-grpc /usr/local/bin/

cd $old_dir
```

## Generating code
Once you've got `protoc` with the plugin installed, you can generate PHP code.
In general it's done like this: 
```
protoc --php_out=Contract/ --php-grpc_out=Contract/ -I path/to/import/root/dir path/to/service/dir/**/*.proto

# If you've built patched plugin and want to generate interfaces for each method individually,
# add an option: --php-grpc_opt="FilePerMethod=true"
protoc --php_out=Contract/ \
  --php-grpc_out=Contract/ \
  --php-grpc_opt="FilePerMethod=true" \
  -I path/to/import/root/dir path/to/service/dir/**/*.proto
```
*NOTE: If you use FilePerMethod=true option, make sure you apply patch https://github.com/supersmile2009/php-grpc/commit/dab727e8a5fe96a46378a0f459ce4d7893ab8aae.patch
in your composer.json in a project where you're going to use the generated PHP code.*

`protoc` will create folder structure according to PHP namespaces configured in proto files.
Since App namespace is usually mapped to `src` directory, the resulting folder structure may be not entirely correct.
For convenience `bin/generate-contract.sh` script is provided. It assumes that these conventions are always followed:
- All generated files are within `App\Contract` namespace
- `src` dir is mapped to `App` namespace
- Output location is `src/Contract`
Usage:
```shell script
# Compile concrete proto file
bin/generate-contract.sh path/to/import/dir path/to/protos/to/compile.proto
# Compile all proto files in a dir
bin/generate-contract.sh path/to/import/dir path/to/protos
```
