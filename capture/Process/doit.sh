#!/usr/bin/env bash
#
# Script to execute the generation of the dataset
#
# Author: Abelardo Pardo <abelardo.pardo@uc3m.es>

pla_dir="../../PLA/process"
export PYTHONPATH=.:$pla_dir

rule_file="Rules.cfg"

if [ "$1" != "" ]; then
    rule_file="$1"
fi

time python $pla_dir/update_events.py $rule_file
