#!/bin/sh
set -eu

PLUGIN="wp-content/plugins/ruwah-product-images/ruwah-product-images.php"

grep -q "Version: 2.4.0" "$PLUGIN"
grep -q "WP_CLI::add_command('ruwah product-images'" "$PLUGIN"
grep -q "function begin" "$PLUGIN"
grep -q "function stage" "$PLUGIN"
grep -q "function apply" "$PLUGIN"
grep -q "function status" "$PLUGIN"
grep -q "function restore" "$PLUGIN"
if grep -Eq "add_action\(['\"](plugins_loaded|init)['\"].*(migrate|process_batch)" "$PLUGIN"; then
    echo "Product migration must not run from a public WordPress request hook" >&2
    exit 1
fi
