<div id="customer" class="pos-pane">
    <div class="row">
        <div class="col-6">
            <div class="pos-pane-list">
                <div class="d-flex align-items-center mb-3">
                    <h5 class="mb-0 mr-2">Customers</h5>
                    <input type="text" class="form-control" placeholder="Search by name or phone" v-model="customer_search_string" @keyup="debounceCustomerSearch">
                </div>
                <div style="max-height: calc(100vh - 180px); overflow-y: auto">
                    <div class="customerInfo" v-for="(row, index) in customers" :key="row.id">
                        <div class="d-flex justify-content-between">
                            <h5 class="customerName mb-1">@{{ row.name }}</h5>
                            <span class="pos-edit-link" @click="editCustomer(row)">Edit</span>
                        </div>
                        <div class="text-muted">@{{ row.mobile }} <span v-if="row.email">· @{{ row.email }}</span></div>
                    </div>
                    <div v-if="customers.length < 1" class="text-muted p-3">No customers found.</div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="pos-pane-detail">
                <h5 class="mb-3">@{{ editableCustomerId ? 'Edit customer' : 'New customer' }}</h5>
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Name" v-model="newCustomer.name">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Email" v-model="newCustomer.email">
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" name="phone" id="phone" class="form-control" placeholder="Phone" v-model="newCustomer.mobile">
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" name="address" id="address" class="form-control" placeholder="Address" v-model="newCustomer.address">
                </div>
                <div class="form-group">
                    <label for="dob">Date of birth</label>
                    <input type="date" name="dob" id="dob" class="form-control" v-model="newCustomer.dob">
                </div>
                <div class="form-group">
                    <label for="doa">Anniversary</label>
                    <input type="date" name="doa" id="doa" class="form-control" v-model="newCustomer.doa">
                </div>
                <button class="btn saveButton" @click="submitCustomerInfo">Save customer</button>
            </div>
        </div>
    </div>
</div>
