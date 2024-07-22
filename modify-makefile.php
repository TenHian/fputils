<?php
if ($argc < 2) {
    echo 'frankenphp.exe php-cli "Makefile path"';
}
$sourceFile = $argv[1];

if (file_exists($sourceFile)) {
    $content = file_get_contents($sourceFile);

    $content = preg_replace('/^SAPI_TARGETS=.*/m', 'SAPI_TARGETS=php.exe php8embed.dll', $content);

    $content = str_replace(
        'php8embed.lib: $(BUILD_DIR)\php8embed.lib',
        'php8embed.dll: $(BUILD_DIR)\php8embed.dll',
        $content
    );

    $content = str_replace(
        '$(BUILD_DIR)\php8embed.lib: $(DEPS_EMBED) $(EMBED_GLOBAL_OBJS) $(BUILD_DIR)\$(PHPLIB) $(BUILD_DIR)\php8embed.lib.res $(BUILD_DIR)\php8embed.lib.manifest',
        '$(BUILD_DIR)\php8embed.dll: $(DEPS_EMBED) $(EMBED_GLOBAL_OBJS) $(BUILD_DIR)\$(PHPLIB) $(BUILD_DIR)\php8embed.lib.res $(BUILD_DIR)\php8embed.lib.manifest',
        $content
    );

    $content = str_replace(
        '@$(MAKE_LIB) /nologo /out:$(BUILD_DIR)\php8embed.lib $(ARFLAGS) $(EMBED_GLOBAL_OBJS_RESP) $(BUILD_DIR)\$(PHPLIB) $(ARFLAGS_EMBED) $(LIBS_EMBED) $(BUILD_DIR)\php8embed.lib.res',
        '@"$(LINK)" /nologo /out:$(BUILD_DIR)\php8embed.dll /DLL $(ARFLAGS) $(EMBED_GLOBAL_OBJS_RESP) $(BUILD_DIR)\$(PHPLIB) $(ARFLAGS_EMBED) $(LIBS_EMBED) $(BUILD_DIR)\php8embed.lib.res',
        $content
    );

    // write back
    file_put_contents($sourceFile, $content);
    echo "Makefile has been modified successfully.";
} else {
    echo "Makefile does not exist at the specified path.";
}
?>
