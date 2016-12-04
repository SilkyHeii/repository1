class AddMailToEvents < ActiveRecord::Migration
  def change
    add_column :events, :mail, :string
  end
end
