Cal3::Application.routes.draw do
  get "keystates/show"

  resources :users
  resources :login
  resources :chat

  resources :events do
    collection do
      get 'create_mail'
    end
  end

  root to: 'calendar#index'

  get 'inquiry' => 'inquiry#index'              # 入力画面
  post 'inquiry/confirm' => 'inquiry#confirm'   # 確認画面
  post 'inquiry/comp' => 'inquiry#comp'     # 送信完了画面
  post 'calendar/removetweet' => 'calendar#removetweet'	#ツイート削除
  post 'calendar/chat' => 'calendar#chat'
  get "/keystate" => "keystates#show"

  post 'nochatcalendar' =>"nochatcalendar#index"


  match '/calendar(/:year(/:month))' => 'calendar#index', :as => :calendar, :constraints => {:year => /\d{4}/, :month => /\d{1,2}/} #最初はこれ　現在のは下

  #map.calendar_day "/calendar/:year/:month/:day", :controller => "calendar", :action => "day"

  match "/events/new/:year/:month/:day/", :controller => "events", action: "day"
    #match "/events/mail/" , :controller => "events", action: "mail"
  # The priority is based upon order of creation:
  # first created -> highest priority.

  # Sample of regular route:
  #   match 'products/:id' => 'catalog#view'
  # Keep in mind you can assign values other than :controller and :action

  # Sample of named route:
  #   match 'products/:id/purchase' => 'catalog#purchase', :as => :purchase
  # This route can be invoked with purchase_url(:id => product.id)

  # Sample resource route (maps HTTP verbs to controller actions automatically):
  #   resources :products

  # Sample resource route with options:
  #   resources :products do
  #     member do
  #       get 'short'
  #       post 'toggle'
  #     end
  #
  #     collection do
  #       get 'sold'
  #     end
  #   end

  # Sample resource route with sub-resources:
  #   resources :products do
  #     resources :comments, :sales
  #     resource :seller
  #   end

  # Sample resource route with more complex sub-resources
  #   resources :products do
  #     resources :comments
  #     resources :sales do
  #       get 'recent', :on => :collection
  #     end
  #   end

  # Sample resource route within a namespace:
  #   namespace :admin do
  #     # Directs /admin/products/* to Admin::ProductsController
  #     # (app/controllers/admin/products_controller.rb)
  #     resources :products
  #   end

  # You can have the root of your site routed with "root"
  # just remember to delete public/index.html.
  # root :to => 'welcome#index'

  # See how all your routes lay out with "rake routes"

  # This is a legacy wild controller route that's not recommended for RESTful applications.
  # Note: This route will make all actions in every controller accessible via GET requests.
  match ':controller(/:action(/:id))(.:format)'
 
 # Rails.application.routes.draw do
  get "keystates/show"

    if Rails.version >= '4.0.0'
      match "/websocket", :to => WebsocketRails::ConnectionManager.new, via: [:get, :post]
    else
       match "/websocket", :to => WebsocketRails::ConnectionManager.new
    end
  

end
