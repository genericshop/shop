<?php
// Correct Rest file to edit
class App_Service_Rest 
{
	//Test: http://197.242.150.225:8083/rest/
    //Production: http://41.86.98.148/bookshop/rest/   http://154.0.163.97/rest/

    protected $_host = 'http://154.0.163.97/rest/';
    protected $_uri  = null;
    
    private $store_id;
    
    public function __construct($store_id)
    {
        $this->store_id = $store_id;
    }
    
    private function doRequest($uri, array $data = array(), $type = 'post')
    {
        // echo $this->_host . $uri;
        
        $ch = curl_init($this->_host . $uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        if (!empty($data) && $type === 'post') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }
        
        $result = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($result);
        
        if (is_object($result) && !isset($result->Status)) {
        
            $result->Status = false;
            
            if (isset($result->message)) {
                $result->StatusMessage = $result->message;
            } else {
                $result->StatusMessage = 'An unknown error has occurred. Please contact us for further assistance';
            }
            
        }
        
        return $result;
    }
    
    // store
    
    public function getStoreDetails()
    {
        // http://41.86.98.148/bookshop/rest/WS_GetStoreDetails?about
        return $this->doRequest('WS_GetStoreDetails?StoreID=' . $this->store_id);
    }
    
    public function getStoreLinks()
    {
        // http://41.86.98.148/bookshop/rest/WS_GetStoreDetails_Link?about
        return $this->doRequest('WS_GetStoreDetails_Link?StoreID=' . $this->store_id);
    }
    
    public function getStoreBanners()
    {
        // http://41.86.98.148/bookshop/rest/WS_GetStoreDetails_Banners?about
        return $this->doRequest('WS_GetStoreDetails_Banners?StoreID=' . $this->store_id);
    }
    
    public function getStoreTerms()
    {
        // http://41.86.98.148/bookshop/rest/WS_GetStoreDetails_TC?about
        return $this->doRequest('WS_GetStoreDetails_TC?StoreID=' . $this->store_id);
    }
    
    public function getStoreNotices()
    {
        // http://41.86.98.148/bookshop/rest/WS_GetStoreDetails_Notice?about
        return $this->doRequest('WS_GetStoreDetails_Notice?StoreID=' . $this->store_id);
    }
    
    public function getStoreBankingDetails()
    {
        // http://41.86.98.148/bookshop/rest/WS_GetBankingDetails?about
        return $this->doRequest('WS_GetBankingDetails?StoreID=' . $this->store_id);
    }
    
    public function getStoreAccountTypes()
    {
        // http://41.86.98.148/bookshop/rest/WS_GetStoreAccountTypes?about
        return $this->doRequest('WS_GetStoreAccountTypes');
    }
    
    public function getAllGrades()
    {
        // http://41.86.98.148/bookshop/rest/WS_GetAllGrades?about
        return $this->doRequest('WS_GetAllGrades');
    }

    public function getAllGradesByStore()
    {
        // http://41.86.98.148/bookshop/rest/WS_GetAllGradesByStore?about
        return $this->doRequest('WS_GetAllGradesByStore?StoreID=' . $this->store_id);
    }
    
    public function getAllSubjects()
    {
        // http://41.86.98.148/bookshop/rest/WS_GetAllSubjects?about
        return $this->doRequest('WS_GetAllSubjects');
    }
    
    public function getAllSubjectsByGrade($grade_id)
    {
        // http://41.86.98.148/bookshop/rest/WS_GetAllSubjectsByGradeAndStore?about
        return $this->doRequest('WS_GetAllSubjectsByGradeAndStore?StoreID=' . $this->store_id . '&GradeID=' . $grade_id);
    }
    
    // accounts
    
    public function getAccountDetails($reference)
    {
        // http://41.86.98.148/bookshop/rest/WS_GetAccountDetails?about
        return $this->doRequest('WS_GetAccountDetails?StoreID=' . $this->store_id . '&UniqueRef=' . $reference);
    }
    
    public function getAccountBalance($reference)
    {
        // http://41.86.98.148/bookshop/rest/WS_GetMyBalance?about
        return $this->doRequest('WS_GetMyBalance?StoreID=' . $this->store_id . '&ParentUniqueRef=' . $reference);
    }
    
    public function registerParentAccount($data)
    {
        // http://41.86.98.148/bookshop/rest/WS_RegisterParentAccount?about
    
        /*
         Name
         Surname
         NationalID
         HomePhone
         CellPhone
         FullAddress
         Email
         Password
         StoreID
         */
    
        $data['StoreID'] = $this->store_id;
        return $this->doRequest('WS_RegisterParentAccount', $data);
    }
    
    public function registerStudentAccount($data)
    {
        // http://41.86.98.148/bookshop/rest/WS_RegisterStudentAccount?about
    
        /*
         Name
         Surname
         Email
         Password
         LearnerNumber
         GradeID
         StoreID
         _ClassID
         */
    
        $data['StoreID'] = $this->store_id;
        return $this->doRequest('WS_RegisterStudentAccount', $data);
    }
    
    public function updateParentAccount($data)
    {
        // http://41.86.98.148/bookshop/rest/WS_ChangeParentDetails?about
    
        /*
         Name
         Surname
         Email
         CellPhone
         Password
         FullAddress
         StoreID
         ParentUniqueRef
         */
    
        $data['StoreID'] = $this->store_id;
        return $this->doRequest('WS_ChangeParentDetails', $data);
    }
    
    public function updateStudentAccount($data)
    {
        // http://41.86.98.148/bookshop/rest/WS_ChangeStudentDetails?about
    
        /*
         LearnerNumber
         Name
         Surname
         CellPhone
         Email
         Password
         GradeID
         StudentUniqueRef
         */
    
        return $this->doRequest('WS_ChangeStudentDetails', $data);
    }
    
    public function addStudentToParent($data)
    {
        // http://41.86.98.148/bookshop/rest/WS_AddStudentToParent?about
    
        /*
         LearnerNumber
         Name
         Surname
         Email
         Password
         StoreID
         Relationship
         GradeID
         _ClassID
         ParentUniqueRef
         */
    
        $data['StoreID'] = $this->store_id;
        return $this->doRequest('WS_AddStudentToParent', $data);
    }
    
    public function linkStudentToParent($data)
    {
        // http://41.86.98.148/bookshop/rest/WS_LinkExistingStudentToParent?about
    
        /*
         LearnerNumber
         ParentUniqueRef
         Relationship
         StoreID
         */
    
        $data['StoreID'] = $this->store_id;
        return $this->doRequest('WS_LinkExistingStudentToParent', $data);
    }
    
    public function getChildrenForParent($parent_id)
    {
        // http://41.86.98.148/bookshop/rest/WS_GetChildrenForParent?about
        return $this->doRequest('WS_GetChildrenForParent?StoreID=' . $this->store_id . '&ParentUniqueRef=' . $parent_id);
    }
    
    public function getChildForParent($parent_id, $student_id)
    {
        // http://41.86.98.148/bookshop/rest/WS_GetChildForParent?about
        return $this->doRequest('WS_GetChildForParent?StoreID=' . $this->store_id . '&ParentUniqueRef=' . $parent_id . '&StudentUniqueRef=' . urlencode($student_id));
    }
    
    public function getSubjectsForStudent($student_id)
    {
        // http://41.86.98.148/bookshop/rest/WS_GetAllSubjectsByStudent?about
        return $this->doRequest('WS_GetAllSubjectsByStudent?StoreID=' . $this->store_id . '&StudentUniqueRef=' . urlencode($student_id));
    }
    
    public function getStudentGrade($student_id)
    {
        // http://41.86.98.148/bookshop/rest/WS_GetStudentGrade?about
        return $this->doRequest('WS_GetStudentGrade?StoreID=' . $this->store_id . '&StudentUniqueRef=' . urlencode($student_id));
    }
    
    public function addStudentSubject($data)
    {
        // http://41.86.98.148/bookshop/rest/WS_AddSubjectToStudent?about
    
        /*
            SubjectID	
            GradeID	
            StudentUniqueRef
            StoreID	
         */
    
        $data['StoreID'] = $this->store_id;
        return $this->doRequest('WS_AddSubjectToStudent', $data);
    }

    public function removeStudentSubject($data)
    {
        // http://41.86.98.148/bookshop/rest/WS_RemoveStudentSubject?about
    
        /*
            SubjectID
            StudentUniqueRef
            StoreID
        */
    
        $data['StoreID'] = $this->store_id;
        return $this->doRequest('WS_RemoveStudentSubject', $data);
    }
    
    // accounts - auth
    
    public function login($data)
    {
        // http://41.86.98.148/bookshop/rest/WS_LoginStoreAccount?about
    
        /*
         Email
         Password
         StoreID
         */
    
        $data['StoreID'] = $this->store_id;
        return $this->doRequest('WS_LoginStoreAccount', $data);
    }
    
    public function forgotPassword($data)
    {
        // http://41.86.98.148/bookshop/rest/WS_ForgotPassword?about
    
        $data['StoreID'] = $this->store_id;
        return $this->doRequest('WS_ForgotPassword', $data);
    }
    
    public function changePassword($data)
    {
        // http://41.86.98.148/bookshop/rest/WS_ChangePassword?about
        return $this->doRequest('WS_ChangePassword', $data);
    }
    
    // books
    
    /*
    public function getCatalogue()
    {
        // http://41.86.98.148/bookshop/rest/WS_GetCatalogue?about
        return $this->doRequest('WS_GetCatalogue');
    }
    
    public function getBooksByCatalogue()
    {
        // http://41.86.98.148/bookshop/rest/WS_GetBooksByCatalogue?about
        return $this->doRequest('WS_GetBooksByCatalogue');
    }
    */
    
    public function getEbooks()
    {
        // http://41.86.98.148/bookshop/rest/WS_GetEBooks?about
        return $this->doRequest('WS_GetEBooks');
    }
    
    public function getBooks()
    {
        // http://41.86.98.148/bookshop/rest/WS_GetBooks?about
        return $this->doRequest('WS_GetBooks');
    }
    
    public function getBook($isbn)
    {
        // http://41.86.98.148/bookshop/rest/WS_GetBook?about
        return $this->doRequest('WS_GetBook?ISBN=' . $isbn);
    }
    
    // products
    
    public function getCategories()
    {
        // http://41.86.98.148/bookshop/rest/WS_GetCatagories?about
        return $this->doRequest('WS_GetCatagories?StoreID=' . $this->store_id);
    }    
    
    public function getSubCategories($category_id)
    {
        // http://41.86.98.148/bookshop/rest/WS_GetSubCatagories?about
        return $this->doRequest('WS_GetSubCatagories?StoreID=' . $this->store_id . '&CatagoryID=' . $category_id);
    }
    
    public function getAllProducts()
    {
        // http://41.86.98.148/bookshop/rest/WS_GetAllProducts?about
        return $this->doRequest('WS_GetAllGrades');
    }
    
    public function getAllProductsByCategory($id)
    {
        // http://41.86.98.148/bookshop/rest/WS_GetAllProductsByCatagory?about
        return $this->doRequest('WS_GetAllProductsByCatagory?StoreID=' . $this->store_id . '&CatagoryID=' . $id);
    }
    
    public function getAllProductsBySubCategory($id)
    {
        // http://41.86.98.148/bookshop/rest/WS_GetAllProductsBySubCatagory?about
        return $this->doRequest('WS_GetAllProductsBySubCatagory?StoreID=' . $this->store_id . '&SubCatagoryID=' . $id);
    }
    
    public function getAllSpecials()
    {
        // http://41.86.98.148/bookshop/rest/WS_GetAllSpecials?about
        return $this->doRequest('WS_GetAllSpecials?StoreID=' . $this->store_id);
    }
    
    public function getCategory($id)
    {
        // http://41.86.98.148/bookshop/rest/WS_GetCatagory?about
        return $this->doRequest('WS_GetCatagory?StoreID=' . $this->store_id . '&CatagoryID=' . $id);
    }
    
    public function getSubCategory($id)
    {
        // http://41.86.98.148/bookshop/rest/WS_GetSubCatagory?about
        return $this->doRequest('WS_GetSubCatagory?StoreID=' . $this->store_id . '&SubCatagoryID=' . $id);
    }
    
    public function getProduct($id)
    {
        // http://41.86.98.148/bookshop/rest/WS_GetProduct?about
        return $this->doRequest('WS_GetProduct?StoreID=' . $this->store_id . '&StockID=' . $id);
    }
    
    // product bundles
    
    public function getPriceLists($grade_id = null)
    {
        if ($grade_id) {
            // http://41.86.98.148/bookshop/rest/WS_GetPriceListsByGrade?about
            return $this->doRequest('WS_GetPriceListsByGrade?StoreID=' . $this->store_id . '&GradeID=' . $grade_id);
        }
        
        // http://41.86.98.148/bookshop/rest/WS_GetPriceLists?about
        return $this->doRequest('WS_GetPriceLists?StoreID=' . $this->store_id);
    }
    
    public function getPriceListItems($list_id)
    {
        // http://41.86.98.148/bookshop/rest/WS_GetPriceListItems?about
        return $this->doRequest('WS_GetPriceListItems?StoreID=' . $this->store_id . '&PriceListID=' . $list_id);
    }
    
    public function getPriceListDetails($list_id)
    {
        // http://41.86.98.148/bookshop/rest/WS_GetPriceListDetails?about
        return $this->doRequest('WS_GetPriceListDetails?StoreID=' . $this->store_id . '&PriceListID=' . $list_id);
    }
    
    public function getPriceListItemDetails($list_id, $stock_id)
    {
        // http://41.86.98.148/bookshop/rest/WS_GetPriceListItemDetails?about
        return $this->doRequest('WS_GetPriceListItemDetails?StoreID=' . $this->store_id . '&PriceListID=' . $list_id . '&StockID=' . $stock_id);
    }
    
    // product/ other
    
    public function getBuyBackItems()
    {
        // http://41.86.98.148/bookshop/rest/WS_GetBuyBackBookPrices?about
        return $this->doRequest('WS_GetBuyBackBookPrices?StoreID=' . $this->store_id);
    }
    
    // orders
    
    public function getTransactions($parent_id)
    {
        // http://41.86.98.148/bookshop/rest/WS_GetTransactions?about
        return $this->doRequest('WS_GetTransactions?StoreID=' . $this->store_id . '&ParentUniqueRef=' . $parent_id);
    }
    
    public function getOrderDetails($reference)
    {
        // http://41.86.98.148/bookshop/rest/WS_GetOrderDetails?about
        return $this->doRequest('WS_GetOrderDetails?StoreID=' . $this->store_id . '&OrderRef=' . $reference);
    }
    
    public function createOrder($data)
    {
        // http://41.86.98.148/bookshop/rest/WS_CreateOrder?about
        
        /*
        OrderDate	
        StoreID	
        AccountUniqueRef	
        PaymentMethot	
        */
        
        $data['StoreID'] = $this->store_id;
        return $this->doRequest('WS_CreateOrder', $data);
    }
    
    public function createOrderItem($data)
    {
        // http://41.86.98.148/bookshop/rest/WS_CreateOrderItem?about
        
        /*
        Status	
        Quantity	
        StockID	
        ISBN	
        Price	
        OrderRef	
        Pricelist	
        Reference	
        StudentUniqueRef	
        DownloadLink	
        ItemDescription	
        */
        
        return $this->doRequest('WS_CreateOrderItem', $data);
    }
    
    public function createPaygatePayment($data)
    {
        // http://41.86.98.148/bookshop/rest/WS_PaygatePayment?about
    
        /*
            Amount	
            StoreID	
            OrderRef	
            PayGateRef	
            ParentUniqueRef	
         */
    
        $data['StoreID'] = $this->store_id;
        return $this->doRequest('WS_PaygatePayment', $data);
    }
    
    public function checkOrderStatus($reference)
    {
        // http://41.86.98.148/bookshop/rest/WS_CheckOrderStatus?about
        return $this->doRequest('WS_CheckOrderStatus?StoreID=' . $this->store_id . '&OrderRef=' . $reference);
    }
    
    public function getParentAccountOrders($parent_id)
    {
        // http://41.86.98.148/bookshop/rest/WS_PreviousOrdersByParent?about
        return $this->doRequest('WS_PreviousOrdersByParent?StoreID=' . $this->store_id . '&ParentUniqueRef=' . $parent_id);
    }
    
    public function getParentAccountOrderItems($parent_id, $reference)
    {
        // http://41.86.98.148/bookshop/rest/WS_PreviousOrderItemsByParent?about
        return $this->doRequest('WS_PreviousOrderItemsByParent?StoreID=' . $this->store_id . '&ParentUniqueRef=' . $parent_id . '&OrderRef=' . $reference);
    }
    
    public function getStudentAccountOrders($student_id)
    {
        // http://41.86.98.148/bookshop/rest/WS_PreviousOrdersByChild?about
        return $this->doRequest('WS_PreviousOrdersByChild?StoreID=' . $this->store_id . '&StudentUniqueRef=' . urlencode($student_id));
    }
    
    public function getParentBooks($parent_id)
    {
        // http://41.86.98.148/bookshop/rest/WS_GetMyBooksParent?about
        return $this->doRequest('WS_GetMyBooksParent?StoreID=' . $this->store_id . '&ParentUniqueRef=' . $parent_id);
    }
    
    public function getStudentBooks($student_id)
    {
        // http://41.86.98.148/bookshop/rest/WS_GetMyBooksStudent?about
        return $this->doRequest('WS_GetMyBooksStudent?StoreID=' . $this->store_id . '&StudentUniqueRef=' . urlencode($student_id));
    }
    
    public function getAccountEbook($reference)
    {
        // http://41.86.98.148/bookshop/rest/WS_GetMyBookDetails?about
        return $this->doRequest('WS_GetMyBookDetails?Reference=' . $reference);
    }

    public function getBBTC()
    {
        //http://41.86.98.148/bookshop/rest/WS_GetBBTC?about
        return $this->doRequest('WS_GetBBTC');
    }
    public function updateLinkParent($data)
    {
        // http://41.86.98.148/bookshop/rest/WS_UpdateLinkParent?about
        
        /*
        Reference
        ParentUniqueRef
        DownloadLink
        */
        
        return $this->doRequest('WS_UpdateLinkParent', $data);
    }
    
    public function updateLinkStudent($data)
    {
        // http://41.86.98.148/bookshop/rest/WS_UpdateLinkStudent?about
    
        /*
         Reference
         StudentUniqueRef
         DownloadLink
         */
    
        return $this->doRequest('WS_UpdateLinkStudent', $data);
    }
    
    // misc
    
    public function logErrors($type, $desc, $code)
    {
        // http://41.86.98.148/bookshop/rest/WS_LogErrors?about
        return $this->doRequest('WS_LogErrors', array(
            'ErrorType'        => $type, 
            'ErrorDescription' => $desc, 
            'ErrorCode'        => $code
        ));
    }
    
}