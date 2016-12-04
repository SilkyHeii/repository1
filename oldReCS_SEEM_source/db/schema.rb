# encoding: UTF-8
# This file is auto-generated from the current state of the database. Instead
# of editing this file, please use the migrations feature of Active Record to
# incrementally modify your database, and then regenerate this schema definition.
#
# Note that this schema.rb definition is the authoritative source for your
# database schema. If you need to create the application database on another
# system, you should be using db:schema:load, not running all the migrations
# from scratch. The latter is a flawed and unsustainable approach (the more migrations
# you'll amass, the slower it'll run and the greater likelihood for issues).
#
# It's strongly recommended to check this file into your version control system.

ActiveRecord::Schema.define(:version => 20160613092608) do

  create_table "events", :force => true do |t|
    t.string   "name"
    t.datetime "start_at"
    t.datetime "end_at"
    t.text     "event"
    t.string   "group"
    t.datetime "created_at", :null => false
    t.datetime "updated_at", :null => false
    t.text     "comment"
    t.string   "Event2"
    t.string   "eventother"
    t.string   "mail"
  end

  create_table "gnames", :force => true do |t|
    t.string   "gname"
    t.datetime "created_at", :null => false
    t.datetime "updated_at", :null => false
  end

  create_table "inquiries", :force => true do |t|
    t.datetime "created_at", :null => false
    t.datetime "updated_at", :null => false
  end

  create_table "keystates", :force => true do |t|
    t.string   "state"
    t.datetime "created_at", :null => false
    t.datetime "updated_at", :null => false
  end

  create_table "logs", :force => true do |t|
    t.string   "username"
    t.string   "logmsg"
    t.datetime "created_at", :null => false
    t.datetime "updated_at", :null => false
    t.string   "gname"
  end

  create_table "users", :force => true do |t|
    t.string   "username"
    t.string   "password"
    t.string   "email"
    t.boolean  "dm"
    t.string   "roles"
    t.datetime "created_at", :null => false
    t.datetime "updated_at", :null => false
    t.string   "userid"
    t.string   "attend"
    t.string   "Color"
    t.string   "color2"
    t.string   "ban_id"
  end

end
