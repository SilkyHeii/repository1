#!/usr/local/rvm/bin/ruby
#coding: utf-8
require "mysql"
db = Mysql::new("158.217.174.9", "calendar", "yqbb9cv3", "calendar_production")
db.query("update users set attend='0'")
