#coding: UTF-8
class CreateGnames < ActiveRecord::Migration
  def change
    create_table :gnames do |t|
      t.string :gname
      t.timestamps
    end
  end
end
