class AddAttendToUsers < ActiveRecord::Migration
  def change
    add_column :users, :attend, :string
  end
end
