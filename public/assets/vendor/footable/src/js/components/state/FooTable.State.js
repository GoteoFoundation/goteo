(function($, F){

	// global int to use if the table has no ID
	var _uid = 0,
	// a hash value for the current url
		_url_hash = (function(str){
			var i, l, hval = 0x811c9dc5;
			for (i = 0, l = str.length; i < l; i++) {
				hval ^= str.charCodeAt(i);
				hval += (hval << 1) + (hval << 4) + (hval << 7) + (hval << 8) + (hval << 24);
			}
			return hval >>> 0;
		})(location.origin + location.pathname);

	F.State = F.Component.extend(/** @lends FooTable.State */{
		/**
		 * The state component adds the ability for the table to remember its basic state for filtering, paging and sorting.
		 * @constructs
		 * @extends FooTable.Component
		 * @param {FooTable.Table} table - The parent {@link FooTable.Table} object for the component.
		 * @returns {FooTable.State}
		 */
		construct: function(table){
			// call the constructor of the base class
			this._super(table, table.o.state.enabled);
			// Change this value if an update to this component requires any stored data to be reset
			this._key = '1';
			/**
			 * The key to use to store the state for this table.
			 * @type {(null|string)}
			 */
			this.key = this._key + (F.is.string(table.o.state.key) ? table.o.state.key : this._uid());
			/**
			 * Whether or not to allow the filtering component to store it's state.
			 * @type {boolean}
			 */
			this.filtering = F.is.boolean(table.o.state.filtering) ? table.o.state.filtering : true;
			/**
			 * Whether or not to allow the paging component to store it's state.
			 * @type {boolean}
			 */
			this.paging = F.is.boolean(table.o.state.paging) ? table.o.state.paging : true;
			/**
			 * Whether or not to allow the sorting component to store it's state.
			 * @type {boolean}
			 */
			this.sorting = F.is.boolean(table.o.state.sorting) ? table.o.state.sorting : true;
		},
		/* PROTECTED */
		/**
		 * Checks the supplied data and options for the state component.
		 * @instance
		 * @protected
		 * @param {object} data - The jQuery data object from the parent table.
		 * @fires FooTable.State#"preinit.ft.state"
		 * @this FooTable.State
		 */
		preinit: function(data){
			var self = this;
			/**
			 * The preinit.ft.state event is raised before the UI is created and provides the tables jQuery data object for additional options parsing.
			 * Calling preventDefault on this event will disable the component.
			 * @event FooTable.State#"preinit.ft.state"
			 * @param {jQuery.Event} e - The jQuery.Event object for the event.
			 * @param {FooTable.Table} ft - The instance of the plugin raising the event.
			 * @param {object} data - The jQuery data object of the table raising the event.
			 */
			this.ft.raise('preinit.ft.state', [data]).then(function(){

				self.enabled = F.is.boolean(data.state)
					? data.state
					: self.enabled;

				if (!self.enabled) return;

				self.key = self._key + (F.is.string(data.stateKey) ? data.stateKey : self.key);

				self.filtering = F.is.boolean(data.stateFiltering) ? data.stateFiltering : self.filtering;

				self.paging = F.is.boolean(data.statePaging) ? data.statePaging : self.paging;

				self.sorting = F.is.boolean(data.stateSorting) ? data.stateSorting : self.sorting;

			}, function(){
				self.enabled = false;
			});
		},
		/**
		 * Gets the state value for the specified key for this table.
		 * @instance
		 * @param {string} key - The key to get the value for.
		 * @returns {(*|null)}
		 */
		get: function(key){
			return JSON.parse(localStorage.getItem(this.key + ':' + key));
		},
		/**
		 * Sets the state value for the specified key for this table.
		 * @instance
		 * @param {string} key - The key to set the value for.
		 * @param {*} data - The value to store for the key. This value must be JSON.stringify friendly.
		 */
		set: function(key, data){
			localStorage.setItem(this.key + ':' + key, JSON.stringify(data));
		},
		/**
		 * Clears the state value for the specified key for this table.
		 * @instance
		 * @param {string} key - The key to clear the value for.
		 */
		remove: function(key){
			localStorage.removeItem(this.key + ':' + key);
		},
		/**
		 * Executes the {@link FooTable.Component#readState} function on all components.
		 * @instance
		 */
		read: function(){
			this.ft.execute(false, true, 'readState');
		},
		/**
		 * Executes the {@link FooTable.Component#writeState} function on all components.
		 * @instance
		 */
		write: function(){
			this.ft.execute(false, true, 'writeState');
		},
		/**
		 * Executes the {@link FooTable.Component#clearState} function on all components.
		 * @instance
		 */
		clear: function(){
			this.ft.execute(false, true, 'clearState');
		},
		/**
		 * Generates a unique identifier for the current {@link FooTable.Table} if one is not supplied through the options.
		 * This value is a combination of the url hash and either the element ID or an incremented global int value.
		 * @instance
		 * @returns {*}
		 * @private
		 */
		_uid: function(){
			var id = this.ft.$el.attr('id');
			return _url_hash + '_' + (F.is.string(id) ? id : ++_uid);
		}
	});

	F.components.register('state', F.State, 700);

})(jQuery, FooTable);