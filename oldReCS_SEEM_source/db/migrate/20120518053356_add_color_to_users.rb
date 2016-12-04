class AddColorToUsers < ActiveRecord::Migration
  def change
    add_column :users, :Color, :string
  end
end
