<div id="customer">
    <div class="row">
        <div class="col-6  header-gap" style="background-color: #cbcbcb21">
            <div class="m-2 mt-3 d-flex">
                <h4 style="margin-right: 10px">Customers</h4>
                <input type="text" class="form-control" placeholder="Search Customer By Name">
            </div>
            <div class="row" style="margin: 10px; max-height: 100vh; overflow-y: auto">
                <div class="col-12 customerInfo"  v-for="(row, index) in customers">
                    <h4 class="customerName">@{{ row.name }}</h4>
                    <span>@{{ row.email }}</span>
                    <span>@{{ row.mobile }}</span>
                    <span style="float: right"  @click="editCustomer(row)">EDIT</span>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="m-2 mb-2">
                <h3>Customer Info </h3>
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Enter Name" v-model="newCustomer.name">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Enter Email" v-model="newCustomer.email">
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" name="phone" id="phone" class="form-control" placeholder="Enter Phone Number" v-model="newCustomer.mobile">
                </div>
                <div class="form-group">
                    <label for="email">Address</label>
                    <input type="text" name="address" id="address" class="form-control" placeholder="Enter Address Line 1" v-model="newCustomer.address">
                </div>
                <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <input type="date" name="dob" id="dob" class="form-control" v-model="newCustomer.dob">
                </div>
                <div class="form-group">
                    <label for="doa">Date of Anniversary</label>
                    <input type="date" name="doa" id="doa" class="form-control" v-model="newCustomer.doa">
                </div>
                <button class="btn saveButton" @click="submitCustomerInfo">SAVE</button>
            </div>

        </div>
    </div>
</div>
