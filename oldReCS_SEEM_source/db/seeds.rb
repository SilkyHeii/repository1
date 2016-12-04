# coding: utf-8

# This file should contain all the record creation needed to seed the database with its default values.
# The data can then be loaded with the rake db:seed (or created alongside the db with db:setup).
#
# Examples:
#
#   cities = City.create([{ name: 'Chicago' }, { name: 'Copenhagen' }])
#   Mayor.create(name: 'Emanuel', city: cities.first)
for i in [1]
  User.create(username: "grid#{i}", userid: "grid#{i}", password: Digest::SHA1.hexdigest("grid#{i}"), roles: 'grid班')
end
=begin
for i in [1,2,3,4,5]
  User.create(username: "net#{i}", userid: "net#{i}", password: Digest::SHA1.hexdigest("net#{i}"), roles: 'net班')
end
for i in [1,2,3,4,5]
  User.create(username: "web#{i}", userid: "web#{i}", password: Digest::SHA1.hexdigest("web#{i}"), roles: 'web班')
end
=end
# データベースに直うちしなくても、これで登録される
