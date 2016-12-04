class AddColor2ToUsers < ActiveRecord::Migration
  def change
    add_column :users, :color2, :string
  end
end
