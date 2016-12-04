class CreateLogs < ActiveRecord::Migration
  def change
    create_table :logs do |t|
      t.string :username
      t.string :logmsg

      t.timestamps
    end
  end
end
