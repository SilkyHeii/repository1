class CreateKeystates < ActiveRecord::Migration
  def change
    create_table :keystates do |t|
      t.integer :id
      t.string :state

      t.timestamps
    end
  end
end
