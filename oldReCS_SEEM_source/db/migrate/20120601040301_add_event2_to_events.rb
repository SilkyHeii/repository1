class AddEvent2ToEvents < ActiveRecord::Migration
  def change
    add_column :events, :Event2, :string
  end
end
