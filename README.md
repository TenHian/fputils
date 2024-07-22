# fputil

tmp store patchs and utils for frankenphp on windows

# Build frankenphp on windows

## Build tool-chain

Make sure that you have a development environment could make php source build complete(`buildconf` & `configure` & `nmake`), by follow php windows build manal([PHP: internals:windows:stepbystepbuild_sdk_2](https://wiki.php.net/internals/windows/stepbystepbuild_sdk_2) and [php SDK README](https://github.com/php/php-sdk-binary-tools/blob/master/README.md)). You coud check it work by next few commands.

```cmd
# CMD that in php SDK environment
cd php-src\x64\Release_TS
php -i 
```

## How I setup php SDK

### Install visual studio 2022

You can also install visual studio 2019 to follow php windows build manal strictly.

If not, install vs2022, make sure you follow [php SDK README #Requirements](https://github.com/php/php-sdk-binary-tools/blob/master/README.md#Requirements), install the module that needed. Next, install the other tools mention by [php SDK README ##Other tools](https://github.com/php/php-sdk-binary-tools/blob/master/README.md#other-tools). Most of them could installed by package manager, and few of them need to install by hand.

### Set up php SDK

```powershell
# recommand use powershell, the windows default one.
git clone https://github.com/php/php-sdk-binary-tools.git c:\php-sdk
cd C:\php-sdk
git checkout php-sdk-2.2.0
phpsdk-vs16-x64.bat
# now in CMD that in php SDK environment, after execute .bat above.
```

```cmd
# CMD that in php SDK environment
phpsdk_buildtree phpdev
# now in C:\php-sdk\phpdev\vs16\x64
git clone https://github.com/php/php-src.git
cd php-src
phpsdk_deps -b 8.3 -u
git checkout php-8.3.7
git checkout -b d8.3.7
buildconf
configure
nmake
cd x64\Release_TS
php -i
# now in C:\php-sdk\phpdev\vs16\x64
git clone https://github.com/php/php-src.git
cd php-src
git checkout php-8.3.7
git checkout -b d8.3.7
buildconf
configure
nmake
cd x64\Release_TS
php -i
```

If its output like it should to be, you PHP SDK is complete.

# Build php libs and headers

If you follow the step ##How I setup php SDK, you need clean repository.

```
# CMD that in php SDK environment
cd C:\php-sdk\phpdev\vs16\x64\php-src
nmake clean-all
```

Clone fputils and apply php-patch

```
# CMD that in php SDK environment
cd
git clone https://github.com/TenHian/fputils.git
cd php-src
cp -r C:\fputils\php-patch ./
git apply --stat php-patch\*.patch
git apply --check php-patch\*.patch
git am --abort
git am php-patch\*.patch
git log
```

configure, nmake, nmake build-devel

```
# CMD that in php SDK environment
# if you first to build run `buildconf`
# in C:\php-sdk\phpdev\vs16\x64\php-src
configure --disable-all --enable-zts --enable-embed --enable-cli --disable-opcache-jit --without-pcre-jit --enable-session --with-mysqlnd --enable-pdo --with-pdo-mysql
# I wanted to write this paragraph as a bat script, but .bat is shit.
# 让他们用二进制的frankenphp
# Makefile path in this command is a example
cd C:\fputils
frankenphp.exe php-cli modify-makefile.php "C:\php-sdk\phpdev\vs16\x64\php-src\Makefile"
nmake
nmake build-devel
```

We need `php8ts.dll` `php8embed.dll` in `php-src\x64\Release_TS` and headers under `php-src\x64\Release_TS\php-8.3.7-devel-vs16-x64\include` .

And you could also do some checks on those dlls, ensure that the symbols what we need is exported.

```
# CMD that in php SDK environment
cd C:\php-sdk\phpdev\vs16\x64\php-src\x64\Release_TS
dumpbin /exports php8ts.dll
dumpbin /exports php8embed.dll
```

# Prepare Msys2 environment

Click [msys2-x86_64-20240507.exe](https://github.com/msys2/msys2-installer/releases/download/2024-05-07/msys2-x86_64-20240507.exe) dowload and install it. We use environment **MINGW64/MSYS2**.

## Install basic tools

```bash
# mingw64/msys2 bash
pacman -S git
pacman -S vim
pacman -S make
pacman -S wget
```

## Install gcc

```bash
# mingw64/msys2 bash
pacman -U https://repo.msys2.org/mingw/mingw64/mingw-w64-x86_64-gcc-13.2.0-6-any.pkg.tar.zst
```

## Setup go

```bash
# mingw64/msys2 bash
cd
mv /c/fputils/go /mingw64/lib
cp /c/fputils/go/bin/*.exe /mingw64/bin
```

### Set go environment variables

```bash
# mingw64/msys2 bash
vim ~/.bashrc
```

Add the following two lines to your .bashrc.

```bash
# ~/.bashrc
export GOROOT=/mingw64/lib/go
export GOPATH=C:\\Users\\your user name\\go
```

then

```bash
# mingw64/msys2 bash
source ~/.bashrc
```

If you feel some go env is wrong, just modify it adapt to your environment. Mine blow:

```
set GO111MODULE=on
set GOARCH=amd64
set GOBIN=C:\Users\admin\go\bin
set GOCACHE=C:\Users\admin\AppData\Local\go-build
set GOENV=C:\Users\admin\AppData\Roaming\go\env
set GOEXE=.exe
set GOEXPERIMENT=
set GOFLAGS=
set GOHOSTARCH=amd64
set GOHOSTOS=windows
set GOINSECURE=
set GOMODCACHE=C:\Users\admin\go\pkg\mod
set GONOPROXY=
set GONOSUMDB=
set GOOS=windows
set GOPATH=C:\Users\admin\go
set GOPRIVATE=
set GOPROXY=https://proxy.golang.org,direct
set GOROOT=C:/msys64/mingw64/lib/go
set GOSUMDB=sum.golang.org
set GOTMPDIR=
set GOTOOLCHAIN=auto
set GOTOOLDIR=C:\msys64\mingw64\lib\go\pkg\tool\windows_amd64
set GOVCS=
set GOVERSION=go1.22.2
set GCCGO=gccgo
set GOAMD64=v1
set AR=ar
set CC=gcc
set CXX=g++
set CGO_ENABLED=1
set GOMOD=NUL
set GOWORK=
set CGO_CFLAGS=-O2 -g
set CGO_CPPFLAGS=
set CGO_CXXFLAGS=-O2 -g
set CGO_FFLAGS=-O2 -g
set CGO_LDFLAGS=-O2 -g
set PKG_CONFIG=pkg-config
set GOGCCFLAGS=-m64 -mthreads -Wl,--no-gc-sections -fmessage-length=0 -ffile-prefix-map=C:\msys64\tmp\go-build3468928067=/tmp/go-build -gno-record-gcc-switches
```

## Install brotli

```bash
# mingw64/msys2 bash
pacman -S mingw-w64-x86_64-brotli
```

# Frankenphp dependencies

## Set libs

Copy your `php8ts.dll` and `php8embed.dll` into `/usr/local/lib` , my php-src path is "C:\php-sdk\phpdev\vs16\x64\php-src", I should execute:

```bash
# migw64/msys2 bash
cp /c/php-sdk/phpdev/vs16/x64/php-src/x64/Release_TS/*.dll /usr/local/lib
```

Copy your `libbrotlicommon.a` `libbrotlidec.a` `libbrotlienc.a` to `/usr/local/lib`, my command is:

```bash
# mingww64/msys2 bash
cp /mingw64/lib/libbrotlicommon.a /mingw64/lib/libbrotlidec.a /mingw64/lib/libbrotlienc.a /usr/local/lib
```

## Set headers

Copy all things that under your `php-src/x64/Release_TS/php-8.3.7-devel-vs16-x64/inlude` to `/usr/local/include/php`, my command is:

```bash
# mingww64/msys2 bash
cp /c/php-sdk/phpdev/vs16/x64/php-src/x64/Release_TS/php-8.3.7-devel-vs16-x64/include/* /usr/local/include/php
```

then modify `/usr/local/include/php/main/php.h`

```c
// orign line 95
typedef int pid_t;
// modify it to
typedef long long pid_t;
```

# Prepare and build frankenphp

## Clone and make new branch

```bash
# mingw64/msys2 bash
cd
git clone https://github.com/dunglas/frankenphp.git
cd frankenphp
git checkout v1.1.5
git checkout -b d1.1.5
```

## Patch

```bash
# mingw64/msys2 bash
# in ~/frankenphp
cp -r /c/fputils/fp-patch ./
git am --abort
git am fp-patch/*.patch
```

## Build

If your msys2 has installed in default path, and you followed my step, now you can build.

```bash
# mingw64/msys2 bash
cd ~/frankenphp
cd caddy/frankenphp
make build
# for clean
make clean
```

If not, like you installed msys2 in some where other, check the `~/frankenphp/caddy/frankenphp/Makefile`.

# Run demo

Ok, we got frankenphp built, now we could run some demo.

## Phpinfo

Time for a classic session.

Copy you `frankenphp.exe` to a new path. And copy `php8ts.dll` `php8embed.dll`. Like this:

```bash
# mingw64/msys2 bash
tree phpinfo/
```

output:

```
phpinfo
├── frankenphp.exe
├── index.php
├── php8embed.dll
└── php8ts.dll
```

`index.php` has phpinfo. Then run server:

```bash
# mingw64/msys2 bash
./frankenphp php-server
```

Now access 127.0.0.1:80 .

## Adminer

Adminer is an opensource, singal file, mutli-db-supported db manager. Its nice to use it as a demo.

```bash
# mingw64/msys2 bash
wget https://github.com/vrana/adminer/releases/download/v4.8.1/adminer-4.8.1.php
```

If you follow steps above, the exts we have now are just right for running adminer. And steps same to ##phpinfo, just rename adminer.php to index.php.

## Zentao PMS

Zentao PMS is an opensource project management software, a lit bit more complicated than adminer. Its also need more php extensions, this section want to make frankenphp on windows to server bigger project.

### Rebuild php-src

We need more php exts to supprot zentao pms, so rebuild php-src.

If you have closed terminal that build php-src, you should inital it again.

```powershell
cd C:\php-sdk
phpsdk-vs16-x64.bat
# now in CMD that in php SDK environment, after execute .bat above.
```

```cmd
cd phpdev\vs16\x64\php-src
nmake clean-all
configure --disable-all --enable-zts --enable-embed --enable-cli --disable-opcache-jit --without-pcre-jit --enable-session --with-mysqlnd --enable-pdo --with-pdo-mysql --enable-filter --enable-mbstring --enable-zlib --with-gd --with-iconv --with-openssl --with-curl --enable-ctype
cd c:\fputils
frankenphp.exe php-cli modify-makefile.php "C:\php-sdk\phpdev\vs16\x64\php-src\Makefile"
nmake
nmake build-devel
```

Then, follow the section #Frankenphp dependencies.

### Rebuild frankenphp

```bash
# mingw64/msys2 bash
cd ~/frankenphp/caddy/frankenphp
make clean
make build
```

### Run Zentao PMS

```bash
# mingw64/msys2 bash
mkdir ~/zentao
cd zentao
wget https://github.com/easysoft/zentaopms/archive/refs/tags/zentaopms_18.12.tar.gz
tar -zxvf zentaopms_18.12.tar.gz
cp ~/frankenphp/caddy/frankenphp/frankenphp.exe ./
cp /usr/local/lib/*.dll ./
# copy dll that need by php8ts.dll
cp /c/php-sdk/phpdev/vs16/x64/deps/bin/libcrypto-3-x64.dll ./
cp /c/php-sdk/phpdev/vs16/x64/deps/bin/libssh2.dll ./
cp /c/php-sdk/phpdev/vs16/x64/deps/bin/libssl-3-x64.dll ./
cp /c/php-sdk/phpdev/vs16/x64/deps/bin/nghttp2.dll ./
# copy extension php_openssl
mkdir ext
cp /c/php-sdk/phpdev/vs16/x64/php-src/x64/Release_TS/php_openssl.dll ./ext/
# run zentao pms
./frankenphp.exe php-server --root zentaopms/www
```

Now you will see install page.

# Test

I've modified the php-src/run-tests.php to fit frankenphp, like frankenphp no need to run sapi tests. So far I've only tested frankenphp with no ext. Even so, I didn't modify run-tests.php to the point where it adapts perfectly to frankenphp, there still some tests failed but not frankenphp's problem. Modify run-tests.php is a boring job.

## Rebuild php with no ext

If you have closed terminal that build php-src, you should inital it again.

```cmd
cd C:\php-sdk
phpsdk-vs16-x64.bat
# now in CMD that in php SDK environment, after execute .bat above.
```

```cmd
cd phpdev\vs16\x64\php-src
nmake clean-all
configure --disable-all --enable-zts --enable-embed --enable-cli --disable-opcache-jit --without-pcre-jit
cd c:\fputils
frankenphp.exe php-cli modify-makefile.php "C:\php-sdk\phpdev\vs16\x64\php-src\Makefile"
nmake
nmake build-devel
```

Then, follow the section #Frankenphp dependencies.

## Rebuild frankenphp

```bash
# mingw64/msys2 bash
cd ~/frankenphp/caddy/frankenphp
make clean
make build
```

## Copy files

Copy frankenphp.exe php8ts.dll php8embed.dll into php-src

```bash
# mingw64/msys2 bash
cp ~/frankenphp/caddy/frankenphp/frankenphp.exe /c/php-sdk/phpdev/vs16/x64/php-src
cp /c/php-sdk/phpdev/vs16/x64/php-src/x64/Release_TS/*.dll /c/php-sdk/phpdev/vs16/x64/php-src
```

## Make patch

If follow section #(Build php libs and headers) you already use all patches. If not the patch that modify is 0002-modify-run-tests.php.patch .

## Run-tests

```bash
# mingw64/msys2 bash
cd /c/php-sdk/phpdev/vs16/x64/php-src
./frankenphp php-cli run-tests.php
```

I remember 89% passed.
