import matplotlib.pyplot as plt
from sklearn import svm
from sklearn import tree
import numpy as np
from sklearn.externals.six import StringIO
from StringIO import StringIO
import pydot 

def avg_perceptron_train(feats, labels, maxIters):
    weights=[1]*len(feats[0])
    totalWeights=[0.0]*len(feats[0])
    errors=0
    iters=0
    vectors=0
    hasError=True
    while(hasError and (iters < maxIters)):
        iters+=1
        hasError=False
        for k in range(len(feats)):
            feat=feats[k]
            dotProd=0
            predict=-1
            
            for i in range(len(feat)):
                dotProd+= float(feat[i])*weights[i]
            #print("Game "+ str(k)+ " "+ str(feat[15])+ ","+ str(feat[33]))

            #set prediction
            if dotProd >=0:
                predict=1
            else:
                predict=-1

            #check prediciton
            if(predict != int(labels[k])):
                hasError=True
                errors+=1
                for j in range(len(feat)):
                    weights[j]= weights[j]+(float(feat[j])*int(labels[k]))
                    
            for j in range(len(weights)):
                totalWeights[j] += weights[j]
            vectors+=1
    for k in range(len(totalWeights)):
        totalWeights[k] = totalWeights[k]/vectors
    
    return totalWeights,errors,iters 


def perceptron_test(w, data, labels):
    errors=0
    for k in range(len(data)):
            feat=data[k]
            dotProd=0
            for i in range(len(feat)):
                dotProd+= float(feat[i])*w[i]
            if dotProd >=0:
                predict=1
            else:
                predict=-1
            
            if(predict != int(labels[k])):
                errors+=1
    return float(errors)
    
        
                
            
def main():
    print("reading..")
    data= open("TRAIN-winpcnt-cor.txt", "r")
    tests=open("TEST-winpcnt-cor.txt", "r")
    games=[]
    gamesTree=[]
    testGamesTree=[]
    testGames=[]
    labels=[]
    testLabels=[]
    maxIterations=7

    #parse the text files and get feature vectors and labels
    for line in data:
        line= line.rstrip()
        game_info= line[2:].split(',')
##        game_info.pop(35)
##        game_info.pop(34)
##        game_info.pop(17)
##        game_info.pop(16)
        games.append(game_info)
        labels.append(int(line[0]))
    for line in tests:
        line= line.rstrip()
        test_game_info= line[2:].split(',')
##        test_game_info.pop(35)
##        test_game_info.pop(34)
##        test_game_info.pop(17)
##        test_game_info.pop(16)
        testGames.append(test_game_info)
        testLabels.append(int(line[0]))
    print("got all the feats yo")
    
   #get the labels
##    for i in range(len(labels)):
##        if labels[i]==0:
##            labels[i]=-1          
##    for i in range(len(testLabels)):
##       if testLabels[i]==0:
##            testLabels[i]=-1

    print("got all the labels yo")
    '''
    print("creating diff vectors")
    for game in games:
        diff_game=[]
        for k in range(len(game)/2):
            diff_game.append(float(game[k])+float(game[k+16]))
        gamesTree.append(diff_game)

    for game in testGames:
        diff_game=[]
        for k in range(len(game)/2):
            diff_game.append(float(game[k])+float(game[k+16]))
        testGamesTree.append(diff_game)
    print("done getting differences!")
    '''              
    validStart= len(games)-1230
    ranges=[1216,2432,3648,4864, 6080,7296, validStart]
    train_error=[]
    valid_error=[]
    test_error=[]
    tree_valid_error=[]
    tree_test_error=[]
    kernel_valid_error= []
    kernel_test_error= []
    for i in range(len(ranges)):
        N= ranges[i]
        print(N)
        train=games[0:N]
        trainLabels=labels[0:N]
        validation=games[validStart:]
        validLabels=labels[validStart:]

        #change to diff vectors for tree and kernel
        trainTree=gamesTree[:N]
        validationTree= gamesTree[validStart:]
        
        
        Avg_Weights_Errors_Iters=avg_perceptron_train(train, trainLabels, maxIterations)
        avgWeights = Avg_Weights_Errors_Iters[0]
        train_error.append(100*(perceptron_test(avgWeights, train, trainLabels)/len(train)))
        
        print("got the avg weight vector")
        
        validError_avg= perceptron_test(avgWeights, validation, validLabels)
        valid_error.append(100*(validError_avg/len(validation)))

        test_err= perceptron_test(avgWeights, testGames, testLabels)
        test_error.append(100*(test_err/len(testGames)))
        
        print("Averaged:")
        print("It got " + str(perceptron_test(avgWeights, train, trainLabels)) +" wrong out of "+ str(len(train)))
        print("It got " + str(validError_avg) +" wrong out of " + str(len(validation)))
        print("It got " + str(test_err) +" wrong out of " + str(len(testGames)))

        
        #Decision tree
        print("building tree stuff")
        n_valid= np.array(validation)
        n_valLabs= np.array(validLabels)
        n_feat= np.array(train)
        n_labels= np.array(trainLabels)
        n_test = np.array(testGames)
        n_testLabs= np.array(testLabels)
        #training
        sport_tree= tree.DecisionTreeClassifier(criterion='gini', max_depth=3)
        sport_tree= sport_tree.fit(n_feat,n_labels)
        #output visualization
 #       out= StringIO()
  #      out= tree.export_graphviz(sport_tree, out_file=out)
 #       file_name= str(N)+ "_games_TRAIN_gini_tree_depth_3.pdf"
 #       pydot.graph_from_dot_data(out.getvalue()).write_pdf(file_name)
        #validation
        print("valid error")
        print(sport_tree.score(n_valid,n_valLabs))
        tree_valid_error.append(100*(1-sport_tree.score(n_valid,n_valLabs)))
        print("test error")
        print(sport_tree.score(n_test,n_testLabs))
        tree_test_error.append(100*(1-sport_tree.score(n_test,n_testLabs)))
        print
        

        #Gaussian kernel
        print("doing kernel")
        clsfr= svm.SVC()
        clsfr.fit(train,trainLabels)
        kernel_valid_error.append(100*(1-clsfr.score(validation,validLabels)))
        kernel_test_error.append(100*(1-clsfr.score(testGames,testLabels)))
        print(clsfr.score(validation,validLabels))
        print(clsfr.score(testGames,testLabels))
        print("\n")
        
    print("done")
    
    #plot train error vs train size
    plt.plot(ranges, train_error, "g" )
    plt.plot(ranges, valid_error, "r" )
    plt.plot(ranges, test_error, "b" )
    plt.axis([0,10000,20,50])
    plt.ylabel("error")
    plt.xlabel("train size")
    plt.legend(["training error", "validation error", "test error"], loc="lower right")
    plt.title("Error VS. Train Size")
    plt.show()
    
    
    #plot tree error
    plt.plot(ranges, tree_valid_error, "r" )
    plt.plot(ranges, tree_test_error, "g" )
    plt.axis([0,10000,30,45])
    plt.ylabel("error")
    plt.xlabel("train size")
    plt.title("Error VS. Train Size (Decision Tree)")
    plt.legend(["validation error", "test error"], loc="lower right")
    plt.show()

    #plot kernel error
    plt.plot(ranges, kernel_valid_error, "r" )
    plt.plot(ranges, kernel_test_error, "g" )
    plt.axis([0,10000,30,45])
    plt.ylabel("error")
    plt.xlabel("train size")
    plt.title("Error VS. Train Size (Gaussian Kernel)")
    plt.legend(["validation error", "test error"], loc="lower right")
    plt.show()
    
    
main()






