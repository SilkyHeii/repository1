class AddBanIdToUsers < ActiveRecord::Migration
  def change
    add_column :users, :ban_id, :string
  end
end
