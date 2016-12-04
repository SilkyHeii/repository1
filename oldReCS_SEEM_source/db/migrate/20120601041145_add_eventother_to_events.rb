class AddEventotherToEvents < ActiveRecord::Migration
  def change
    add_column :events, :eventother, :string
  end
end
