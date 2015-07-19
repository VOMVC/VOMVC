# AMVC - Axori MVC

This is a straight forward MVC that follows the below flow, and is extendable on all levels quite easily.


                                             [  MODEL (O)  ]
                                           /   /¯         \  ¯\
           [RESULT (A,R), FILE (R,O)]¯¯¯¯¯/_  /¯[TPL (A)]-_\   \¯¯¯¯¯[ACTION (O), FILE (O), DATA (R,O)]
                                    [VIEW (O)]<--------->[CONTROLLER (R)]
                                     /¯    ¯\            /   /¯
                              [CSS (O)]    [JS (O)]     /_  /¯¯¯¯¯[ROUTER (R)]              
                                                    [USER (A,R)]
